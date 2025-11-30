<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard Group
            'Dashboard' => [
                [
                    'name' => 'View Dashboard',
                    'description' => 'Access to view the dashboard overview',
                ],
            ],

            // Users Management Group
            'Users' => [
                [
                    'name' => 'View Users',
                    'description' => 'View list of users',
                ],
                [
                    'name' => 'Create Users',
                    'description' => 'Create new users',
                ],
                [
                    'name' => 'Edit Users',
                    'description' => 'Edit existing users',
                ],
                [
                    'name' => 'Delete Users',
                    'description' => 'Delete users',
                ],
                [
                    'name' => 'Manage User Roles',
                    'description' => 'Assign or remove roles from users',
                ],
                [
                    'name' => 'Update User Status',
                    'description' => 'Activate or deactivate users',
                ],
            ],

            // Roles & Permissions Group
            'Roles' => [
                [
                    'name' => 'View Roles',
                    'description' => 'View list of roles and their permissions',
                ],
                [
                    'name' => 'Create Roles',
                    'description' => 'Create new roles',
                ],
                [
                    'name' => 'Edit Roles',
                    'description' => 'Edit existing roles and permissions',
                ],
                [
                    'name' => 'Delete Roles',
                    'description' => 'Delete roles',
                ],
                [
                    'name' => 'Manage Permissions',
                    'description' => 'Create and manage permissions',
                ],
            ],

            // Events Management Group
            'Events' => [
                [
                    'name' => 'View Events',
                    'description' => 'View list of events',
                ],
                [
                    'name' => 'View All Events',
                    'description' => 'Access to all events listing page',
                ],
                [
                    'name' => 'View Event Details',
                    'description' => 'View detailed information about events',
                ],
                [
                    'name' => 'Edit Events',
                    'description' => 'Edit event information',
                ],
                [
                    'name' => 'Update Event Market Time',
                    'description' => 'Update market time for events',
                ],
                [
                    'name' => 'Bulk Update Events',
                    'description' => 'Perform bulk updates on events',
                ],
                [
                    'name' => 'Export Events',
                    'description' => 'Export events to CSV',
                ],
            ],

            // Markets Management Group
            'Markets' => [
                [
                    'name' => 'View Markets',
                    'description' => 'View list of markets',
                ],
                [
                    'name' => 'View All Markets',
                    'description' => 'Access to all markets listing page',
                ],
                [
                    'name' => 'View Market Details',
                    'description' => 'View detailed information about markets',
                ],
                [
                    'name' => 'Export Markets',
                    'description' => 'Export markets to CSV',
                ],
            ],

            // Market Rates Group
            'Market Rates' => [
                [
                    'name' => 'View Market Rates',
                    'description' => 'View market rates listing',
                ],
                [
                    'name' => 'View Market Rate Details',
                    'description' => 'View detailed market rate information',
                ],
                [
                    'name' => 'Export Market Rates',
                    'description' => 'Export market rates to CSV',
                ],
            ],

            // Risk Management Group
            'Risk' => [
                [
                    'name' => 'View Risk Markets',
                    'description' => 'View pending and completed risk markets',
                ],
                [
                    'name' => 'Manage Risk Labels',
                    'description' => 'Update labels on risk markets (4x, b2c, b2b, USDT)',
                ],
                [
                    'name' => 'Mark Risk as Done',
                    'description' => 'Mark risk markets as completed with remarks',
                ],
            ],

            // System Logs Group
            'System Logs' => [
                [
                    'name' => 'View System Logs',
                    'description' => 'View application system logs',
                ],
                [
                    'name' => 'View Database Logs',
                    'description' => 'View database system logs',
                ],
                [
                    'name' => 'Delete Database Logs',
                    'description' => 'Delete old database logs',
                ],
                [
                    'name' => 'Download System Logs',
                    'description' => 'Download log files',
                ],
                [
                    'name' => 'Clear System Logs',
                    'description' => 'Clear log files',
                ],
            ],

            // Performance Monitoring Group
            'Performance' => [
                [
                    'name' => 'View Performance Metrics',
                    'description' => 'View system performance metrics',
                ],
                [
                    'name' => 'Refresh Performance Data',
                    'description' => 'Refresh performance monitoring data',
                ],
            ],

            // Settings Group
            'Settings' => [
                [
                    'name' => 'View Settings',
                    'description' => 'Access general settings',
                ],
                [
                    'name' => 'Manage General Settings',
                    'description' => 'Update general application settings',
                ],
                [
                    'name' => 'Clear Cache',
                    'description' => 'Clear application cache',
                ],
                [
                    'name' => 'Optimize Application',
                    'description' => 'Run application optimization commands',
                ],
            ],

            // Profile Management Group
            'Profile' => [
                [
                    'name' => 'View Own Profile',
                    'description' => 'View own user profile',
                ],
                [
                    'name' => 'Edit Own Profile',
                    'description' => 'Update own profile information',
                ],
                [
                    'name' => 'Change Own Password',
                    'description' => 'Change own account password',
                ],
                [
                    'name' => 'Manage Two-Factor Authentication',
                    'description' => 'Enable or disable 2FA for own account',
                ],
                [
                    'name' => 'Manage Own Sessions',
                    'description' => 'View and terminate own active sessions',
                ],
            ],

            // Notifications Management Group
            'Notifications' => [
                [
                    'name' => 'View Notifications',
                    'description' => 'View list of notifications',
                ],
                [
                    'name' => 'Create Notifications',
                    'description' => 'Create new notifications',
                ],
                [
                    'name' => 'Edit Notifications',
                    'description' => 'Edit existing notifications',
                ],
                [
                    'name' => 'Delete Notifications',
                    'description' => 'Delete notifications',
                ],
                [
                    'name' => 'View Notification Details',
                    'description' => 'View detailed notification information',
                ],
                [
                    'name' => 'Mark Notifications as Read',
                    'description' => 'Mark notifications as read',
                ],
                [
                    'name' => 'View Pending Notifications',
                    'description' => 'View pending scheduled notifications',
                ],
                [
                    'name' => 'Manage Push Notifications',
                    'description' => 'View and manage push notifications',
                ],
            ],

            // Settings Management Group
            'Settings Management' => [
                [
                    'name' => 'View Settings',
                    'description' => 'View application settings',
                ],
                [
                    'name' => 'Create Settings',
                    'description' => 'Create new settings',
                ],
                [
                    'name' => 'Edit Settings',
                    'description' => 'Edit existing settings',
                ],
                [
                    'name' => 'Delete Settings',
                    'description' => 'Delete settings',
                ],
            ],

            // Scorecard Management Group
            'Scorecard' => [
                [
                    'name' => 'View Scorecard',
                    'description' => 'View scorecard overview and events',
                ],
                [
                    'name' => 'View Event Markets',
                    'description' => 'View markets for events in scorecard',
                ],
                [
                    'name' => 'Update Scorecard Events',
                    'description' => 'Update event information in scorecard',
                ],
                [
                    'name' => 'Update Scorecard Labels',
                    'description' => 'Update labels on scorecard events',
                ],
            ],

            // Testing Group
            'Testing' => [
                [
                    'name' => 'Access Testing Module',
                    'description' => 'Access testing and debugging tools',
                ],
                [
                    'name' => 'Send Telegram Test Messages',
                    'description' => 'Send test messages via Telegram bot',
                ],
            ],
        ];

        // Create permissions
        foreach ($permissions as $group => $groupPermissions) {
            foreach ($groupPermissions as $permissionData) {
                $slug = Str::slug($permissionData['name']);
                
                Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $permissionData['name'],
                        'description' => $permissionData['description'] ?? null,
                        'group' => $group,
                    ]
                );
            }
        }

        $this->command->info('Permissions seeded successfully!');
        $this->command->info('Total permissions created: ' . Permission::count());
    }
}
