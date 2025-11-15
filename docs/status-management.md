# Status Management Overview

## Table Schema Review

- **events**
  - Key timestamps: `marketTime`, `completeTime`
  - Status flags: `IsSettle`, `IsVoid`, `IsUnsettle`, `isCompleted`, `isRecentlyAdded`
  - No single canonical status column; controllers infer status from flags and time windows.
- **market_lists**
  - Key timestamps: `marketTime`
  - Flags: `isLive`, `isCompleted`, `isRecentlyAdded`
  - String `status` column (default `OPEN`) plus optional `selectionName`, `winnerType`
  - UI often falls back to booleans when `status` is unset.
- **market_rates_{exEventId}** (dynamic per-event tables)
  - Columns: `exMarketId`, `marketName`, `runners` (JSON), `totalMatched`, `inplay`, `isCompleted`, `created_at`
  - No canonical status column; snapshots do not persist market status at capture time.

## Target Status Vocabulary

All three layers should share the same finite set of statuses:

`upcoming`, `in-play`, `settled`, `unsettled`, `closed`, `voided`

## Recommended Model by Layer

- **Event (`events`)**
  - Add a `status` column (enum/text) with default `upcoming`.
  - Treat existing flags as derived values for backward compatibility.
  - Suggested mappings:
    - `upcoming`: `marketTime` in future, not voided/settled.
    - `in-play`: live feed indicates active play or any child market marked `in-play`.
    - `settled`: `IsSettle = 1` or `isCompleted = 1`.
    - `unsettled`: `IsUnsettle = 1` while not voided.
    - `closed`: `completeTime` present but not settled/voided.
    - `voided`: `IsVoid = 1`.
  - Update ingestion/cron logic so status changes cascade to flags and timestamps.

- **Market (`market_lists`)**
  - Normalize `status` column to the shared vocabulary (`UPCOMING`, `IN_PLAY`, `SETTLED`, `UNSETTLED`, `CLOSED`, `VOIDED`).
  - Enforce via database enum or check constraint.
  - Map legacy flags:
    - `isLive = true` → `IN_PLAY`
    - `isCompleted = true` → `SETTLED`
    - `status = 'CLOSED'` stays `CLOSED`
    - `winnerType` only relevant once status is `SETTLED`
  - Controllers/UI should rely on `status` first, falling back to booleans only when null.

- **Rate Snapshots (`market_rates_*`)**
  - Option A: add nullable `status` column and persist parent market status at insert time.
  - Option B: create a view that joins each snapshot to the current `market_lists.status` (less accurate historically, but zero schema change).
  - If column exists, derive fallback when missing: `inplay = true` → `in-play`; `isCompleted = true` → `settled`; else `upcoming`.
  - Update the SQL seeding/creation routine (`RecreateMarketRatesTables`) to include the new column and indexes.

## Implementation Plan

1. **Schema migrations**
   - Add `status` column to `events` with default `upcoming` and backfill from existing flags.
   - Tighten `market_lists.status` via enum/check constraint; backfill to align with vocabulary.
   - Extend dynamic market rate table definition (and seeding SQL) to include nullable `status`.

2. **Data backfill & reconciliation**
   - Write scripts to translate current flags into canonical statuses.
   - Identify and resolve inconsistencies (e.g., event settled but markets still marked `in-play`).

3. **Application updates**
   - Centralize status constants/enums in code to avoid string drift.
   - Refactor controllers, filters, exports, and UI badges to use canonical status fields.
   - Ensure background jobs and ingest processes set statuses consistently and update dependent flags/timestamps.

4. **Monitoring & Tooling**
   - Add admin/reporting views to surface status mismatches across events, markets, and snapshots.
   - Document state transition rules so back-office operations don’t create invalid combinations (e.g., `voided` + `in-play`).

## Residual Considerations

- Keep legacy boolean flags temporarily for downstream systems; mark for deprecation once consumers switch to canonical status.
- Dynamic table creation must be kept in sync with schema changes (command + seed SQL).
- Exports/reports should include the new status field to retain historical context.
