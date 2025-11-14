# Performance Optimization Plan

## Key Pages & Modules
- `/markets` – main market list filtered to today/tomorrow.
- `/markets/all` – full market catalogue (currently slow).
- `/events` – default event list with date filters + recently added toggle.
- `/events/all` – complete event dataset.
- `/market-rates` – SS rates list with event + time filters.
- `/market-rates/{id}` – SS detail view with navigation & screenshots.
- `/markets/export`, `/events/export`, `/market-rates/export` – CSV/Excel downloads.
- Background jobs & console commands (e.g., `market-rates:recreate`, bulk updates) touching dynamic tables.

## Phase Plan

### Phase 1 – Discovery & Baseline (current step)
- Document all DB interactions per route/API.
- Capture slow-query metrics (`EXPLAIN`, logs, sample runtimes).
- Flag missing indexes or heavyweight joins.

### Phase 2 – Critical Path Queries
- ✅ `/markets/all` rewritten to raw SQL (single SELECT for data + COUNT) with reusable filter builder.
- Filters (sport, tournament, market/event name, type, live, pre-bet, recently added, date/time) now translate into parameterized SQL clauses.
- Pagination uses `LIMIT/OFFSET` against the raw query; total count pulled via matching where clause to avoid duplicate builder work.
- Next focus: index rollout (`marketTime`, `(sportName, marketTime)`, `(tournamentsName, marketTime)`, `(isRecentlyAdded, marketTime)`).

### Phase 3 – Event Flows
- ✅ `/events` and `/events/all` now use raw SQL (count + data query) with shared filter builder (`buildEventFilterSql`).
- Status, highlight/popular, recently added, and custom date/time filters translate into parameterized conditions.
- Exports reuse the same raw helper for consistent datasets.
- Next focus: add indices (e.g., `(marketTime)`, `(sportId, marketTime)`, `(tournamentsId, marketTime)`, `(isRecentlyAdded, createdAt)`).

### Phase 4 – SS Rates & Dynamic Tables
- ✅ `/market-rates` index rewritten to raw SQL with helper methods for dynamic table selection, market-name filtering, date/time windows, and pagination.
- ✅ `/market-rates/{id}` navigation uses window-friendly raw queries to fetch next/previous entries and grid mode batches.
- ✅ `/market-rates/export` reuses the raw query helpers for consistent CSV output without loading entire tables into memory.
- Index/action follow-ups: add `(marketName, created_at)` and `(created_at DESC)` indexes per dynamic table; evaluate partitioning strategy for billion-row datasets.

### Phase 5 – Support Jobs & Caching Layer
- Update console commands, cron jobs, and background processes to use the new SQL helpers.
- Add caching for filter metadata/counts to cut repeat hits.

### Phase 6 – Validation & Monitoring
- Regression test each route (functional + performance benchmarks).
- Set up slow-query logging/alerts for ongoing monitoring.

> We’ll proceed phase by phase, reporting findings and changes before moving forward.

---

## Phase 1 Findings (Snapshot)

### `/markets` (main list)
- **Query Shape:** single `DB::table('market_lists')` select (~15 columns). Filters on `sportName`, `tournamentsName`, `eventName`, `marketName/type`, `isLive`, `isPreBet`, `recently_added`, manual date/time window. Default scope limits marketTime to today/tomorrow unless override.
- **Ordering/Pagination:** `ORDER BY marketTime ASC, id ASC` with manual offset/limit.
- **Potential Pain Points:**
  - No covering index for `(marketTime, sportName, tournamentsName, marketName)` plus `isRecentlyAdded`.
  - Date range + `recently_added` toggles rely on `marketTime`/`created_at`; consider composite indexes: `(marketTime)`, `(created_at)`, `(isRecentlyAdded, marketTime)`.
  - Cache lookups for dropdowns already via `Cache::remember`, but `markets.all` shares same caches so misses cause heavy hits.

### `/markets/all`
- **Query Shape:** same base table but without default date window; user filters identical. This route loads the entire dataset, so sort/pagination run over billions of rows.
- **Hot columns:** `marketTime`, `sportName`, `tournamentsName`, `eventName`, `marketName`, `type`, `status`, `isRecentlyAdded`.
- **Action Items:**
  - Move to raw SQL selecting only required columns.
  - Add indexes: `(marketTime DESC)`, `(sportName, marketTime)`, `(tournamentsName, marketTime)`, `(isRecentlyAdded, marketTime)`.
  - Consider materialized view or daily partitioning on `marketTime` to keep scans bounded.

### `/events` & `/events/all`
- **Query Shape:** raw query built with `DB::table('events')`, filtering by sport/tournament, status flags, recently added. Default view constrains `marketTime` to today/tomorrow unless custom range.
- **Ordering:** `/events` uses `marketTime ASC`; `/events/all` keeps `DESC`.
- **Indexes Needed:** `(marketTime)`, `(sportId, marketTime)`, `(tournamentsId, marketTime)`, `(isRecentlyAdded, createdAt)`. Legacy columns `IsSettle/IsVoid/IsUnsettle` should be covered with indexes or precomputed status field once introduced.

### `/market-rates` (SS list)
- **Query Shape:** Dynamic tables `market_rates_{exEventId}` accessed via raw SQL helper. Filters for marketName, date/time range, grid mode, pagination handled via parameterized clauses.
- **Indexes:** Plan to add `(marketName, created_at)` and `(created_at DESC)` indexes per table to support list and detail lookups.
- **Other Costs:** JSON parsing of `runners` remains; consider deferring heavy parsing until detail view.

### `/market-rates/{id}`
- **Query:** Raw SQL fetch by ID with helper to retrieve next/previous records (same `marketName`). Grid mode fetches sequential rows via ORDER BY + LIMIT.
- **Future Work:** optional window-function approach or materialized views if navigation still heavy; enforce indexes noted above.

### Exports & Commands
- **Exports:** reuse same query builders but without pagination (full dataset). Need streaming raw SQL with cursor to avoid memory blowups.
- **Command `market-rates:recreate`:** manipulates tables via raw SQL already; ensure transactions wrapped per table.

### Global Recommendations
1. **Central Query Layer:** create helper that builds raw SQL with bound params for markets/events to avoid repeating logic.
2. **Index Audit:** add DB migration to create composite indexes called out above.
3. **Slow Query Logging:** enable PostgreSQL slow query log or Laravel Telescope for production sampling before/after changes.
4. **Pagination Strategy:** consider keyset pagination (seek) for markets/events to avoid `OFFSET` scans on giant tables.
5. **Caching:** cache expensive filter metadata (counts, dropdowns) longer; consider Redis cache for `market_lists` stats.

Next step: extend the raw-SQL pattern (and supporting indexes) to the `/events` routes.

---

## Phase 2 Progress – `/markets/all`
- Controller now builds raw SQL fragments via `buildMarketFilterSql()` and `resolveMarketDateFilters()`.
- A single COUNT query plus a single SELECT (with bindings) replaces the previous query-builder clone, eliminating duplicate filter parsing and giving DB full control over planning.
- Filters and pagination remain feature-parity with the old implementation; response payloads feed directly into `LengthAwarePaginator`.
- Remaining work: add supporting indexes and repeat the pattern for other high-volume routes (`/events/all`, exports, SS rates).

---

## Phase 3 Progress – `/events` & `/events/all`
- Added `buildEventFilterSql`, `mapEventStatusCondition`, and `resolveEventDateFilters` helpers to convert route filters into raw SQL conditions.
- Both `/events` and `/events/all` now perform a COUNT + SELECT with identical `WHERE` clauses and parameter bindings; no more cloned query builders.
- Export endpoint reuses the same helper, ensuring parity between list and export data.
- Remaining work: create DB migration for the recommended composite indexes to keep scans bounded on production volumes.

---

## Phase 4 Progress – SS Rates & Dynamic Tables
- Created helper utilities to generate parameterized SQL for dynamic `market_rates_{exEventId}` tables, covering list filters, pagination, and exports.
- Applied raw SQL to detail view navigation, reducing repeated queries and giving the DB planner direct control.
- Documented index requirements `(marketName, created_at)` and `(created_at DESC)` for every dynamic table; recommend automation via migration/maintenance job.
- Remaining work: consider partitioning or archiving strategy for historical rate snapshots, and defer JSON parsing until data needs to be rendered.

{
  "cells": [],
  "metadata": {
    "language_info": {
      "name": "python"
    }
  },
  "nbformat": 4,
  "nbformat_minor": 2
}