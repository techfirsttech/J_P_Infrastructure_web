<?php

/**
 * Construction Management System - Module Generator Script
 * 
 * This script generates all required modules for the construction management system
 * following the nwidart/laravel-modules structure.
 */

$modules = [
    'Site' => [
        'description' => 'Construction Site Management Module',
        'tables' => [
            'sites' => [
                'fields' => [
                    'name' => 'string',
                    'code' => 'string:50',
                    'address' => 'text:nullable',
                    'country_id' => 'unsignedBigInteger:nullable',
                    'state_id' => 'unsignedBigInteger:nullable', 
                    'city_id' => 'unsignedBigInteger:nullable',
                    'pincode' => 'string:10:nullable',
                    'project_start_date' => 'date:nullable',
                    'expected_completion_date' => 'date:nullable',
                    'actual_completion_date' => 'date:nullable',
                    'budget_allocated' => 'decimal:15,2:default:0',
                    'budget_spent' => 'decimal:15,2:default:0',
                    'status' => 'enum:planning,active,completed,on-hold:default:planning',
                    'description' => 'text:nullable'
                ],
                'relationships' => ['country', 'state', 'city', 'workers', 'managers', 'equipment', 'materials']
            ],
            'site_managers' => [
                'fields' => [
                    'site_id' => 'unsignedBigInteger',
                    'user_id' => 'unsignedBigInteger',
                    'assigned_date' => 'date',
                    'salary' => 'decimal:10,2:nullable',
                    'status' => 'enum:active,inactive:default:active'
                ],
                'relationships' => ['site', 'user']
            ]
        ]
    ],
    
    'Worker' => [
        'description' => 'Worker Management Module',
        'tables' => [
            'workers' => [
                'fields' => [
                    'name' => 'string',
                    'mobile' => 'string:15',
                    'email' => 'string:nullable',
                    'address' => 'text:nullable',
                    'aadhar_number' => 'string:12:nullable',
                    'pan_number' => 'string:10:nullable',
                    'daily_wage' => 'decimal:8,2',
                    'skill_type' => 'string:nullable',
                    'experience_years' => 'integer:default:0',
                    'site_id' => 'unsignedBigInteger:nullable',
                    'status' => 'enum:active,inactive:default:active',
                    'joining_date' => 'date',
                    'leaving_date' => 'date:nullable'
                ],
                'relationships' => ['site', 'attendances', 'payments']
            ]
        ]
    ],

    'Vendor' => [
        'description' => 'Vendor Management Module',
        'tables' => [
            'vendors' => [
                'fields' => [
                    'name' => 'string',
                    'company_name' => 'string:nullable',
                    'email' => 'string:nullable',
                    'mobile' => 'string:15',
                    'address' => 'text:nullable',
                    'gst_number' => 'string:15:nullable',
                    'pan_number' => 'string:10:nullable',
                    'vendor_type' => 'enum:material,equipment,service:default:material',
                    'credit_limit' => 'decimal:15,2:default:0',
                    'outstanding_amount' => 'decimal:15,2:default:0',
                    'status' => 'enum:active,inactive:default:active'
                ],
                'relationships' => ['materials', 'equipment', 'payments']
            ]
        ]
    ],

    'Material' => [
        'description' => 'Material Management Module',
        'tables' => [
            'materials' => [
                'fields' => [
                    'name' => 'string',
                    'code' => 'string:50',
                    'description' => 'text:nullable',
                    'unit_id' => 'unsignedBigInteger',
                    'category' => 'string:nullable',
                    'rate_per_unit' => 'decimal:10,2',
                    'minimum_stock' => 'integer:default:0',
                    'current_stock' => 'integer:default:0',
                    'status' => 'enum:active,inactive:default:active'
                ],
                'relationships' => ['unit', 'vendors', 'inventories', 'transfers']
            ]
        ]
    ],

    'Equipment' => [
        'description' => 'Equipment Management Module', 
        'tables' => [
            'equipment' => [
                'fields' => [
                    'name' => 'string',
                    'code' => 'string:50',
                    'type' => 'string',
                    'brand' => 'string:nullable',
                    'model' => 'string:nullable',
                    'purchase_date' => 'date:nullable',
                    'purchase_cost' => 'decimal:15,2:nullable',
                    'rental_rate_per_hour' => 'decimal:8,2:nullable',
                    'current_site_id' => 'unsignedBigInteger:nullable',
                    'status' => 'enum:available,in-use,maintenance,repair:default:available',
                    'condition' => 'enum:excellent,good,fair,poor:default:good'
                ],
                'relationships' => ['site', 'maintenances', 'usages']
            ]
        ]
    ],

    'Subcontractor' => [
        'description' => 'Subcontractor Management Module',
        'tables' => [
            'subcontractors' => [
                'fields' => [
                    'name' => 'string',
                    'company_name' => 'string',
                    'contact_person' => 'string:nullable',
                    'email' => 'string:nullable',
                    'mobile' => 'string:15',
                    'address' => 'text:nullable',
                    'gst_number' => 'string:15:nullable',
                    'pan_number' => 'string:10:nullable',
                    'specialization' => 'string:nullable',
                    'rating' => 'decimal:3,2:default:0',
                    'status' => 'enum:active,inactive:default:active'
                ],
                'relationships' => ['contracts', 'payments']
            ]
        ]
    ],

    'Contract' => [
        'description' => 'Contract Management Module',
        'tables' => [
            'contracts' => [
                'fields' => [
                    'contract_number' => 'string',
                    'title' => 'string',
                    'client_name' => 'string',
                    'client_email' => 'string:nullable',
                    'client_mobile' => 'string:15:nullable',
                    'contract_value' => 'decimal:15,2',
                    'start_date' => 'date',
                    'end_date' => 'date',
                    'site_id' => 'unsignedBigInteger:nullable',
                    'status' => 'enum:draft,active,completed,terminated:default:draft',
                    'description' => 'text:nullable'
                ],
                'relationships' => ['site', 'milestones', 'payments']
            ],
            'contract_milestones' => [
                'fields' => [
                    'contract_id' => 'unsignedBigInteger',
                    'title' => 'string',
                    'description' => 'text:nullable',
                    'milestone_date' => 'date',
                    'amount' => 'decimal:15,2',
                    'status' => 'enum:pending,completed:default:pending'
                ],
                'relationships' => ['contract']
            ]
        ]
    ],

    'Ledger' => [
        'description' => 'Ledger Management Module',
        'tables' => [
            'ledgers' => [
                'fields' => [
                    'ledger_type' => 'enum:partner,vendor,worker,site,equipment,subcontractor',
                    'entity_id' => 'unsignedBigInteger',
                    'transaction_type' => 'enum:debit,credit',
                    'amount' => 'decimal:15,2',
                    'transaction_date' => 'date',
                    'reference_type' => 'string:nullable',
                    'reference_id' => 'unsignedBigInteger:nullable',
                    'description' => 'text:nullable',
                    'balance' => 'decimal:15,2:default:0'
                ],
                'relationships' => []
            ]
        ]
    ],

    'Attendance' => [
        'description' => 'Attendance Management Module',
        'tables' => [
            'attendances' => [
                'fields' => [
                    'worker_id' => 'unsignedBigInteger',
                    'site_id' => 'unsignedBigInteger',
                    'attendance_date' => 'date',
                    'attendance_type' => 'enum:full-day,half-day,absent:default:full-day',
                    'check_in_time' => 'time:nullable',
                    'check_out_time' => 'time:nullable',
                    'total_hours' => 'decimal:4,2:default:0',
                    'wage_amount' => 'decimal:8,2:default:0',
                    'notes' => 'text:nullable'
                ],
                'relationships' => ['worker', 'site']
            ]
        ]
    ]
];

echo "=== Construction Management System - Module Generator ===\n\n";
echo "This script will generate the database schema and structure for all modules.\n";
echo "Total modules to generate: " . count($modules) . "\n\n";

foreach ($modules as $moduleName => $moduleConfig) {
    echo "Module: {$moduleName}\n";
    echo "Description: {$moduleConfig['description']}\n";
    
    foreach ($moduleConfig['tables'] as $tableName => $tableConfig) {
        echo "  Table: {$tableName}\n";
        echo "    Fields:\n";
        foreach ($tableConfig['fields'] as $fieldName => $fieldType) {
            echo "      - {$fieldName}: {$fieldType}\n";
        }
        echo "    Relationships: " . implode(', ', $tableConfig['relationships']) . "\n";
    }
    echo "\n";
}

echo "=== Generation Complete ===\n";
echo "Next steps:\n";
echo "1. Run: php artisan module:make ModuleName for each module\n";  
echo "2. Create migrations using the field definitions above\n";
echo "3. Create models with relationships\n";
echo "4. Create controllers with CRUD operations\n";
echo "5. Run migrations: php artisan migrate\n";