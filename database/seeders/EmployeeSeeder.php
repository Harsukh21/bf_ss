<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            [
                'employee_id'    => 'EMP-0001',
                'name'           => 'Harsukh Patel',
                'email'          => 'harsukh@company.com',
                'username'       => 'harsukh',
                'date_of_birth'  => '1995-06-15',
                'gender'         => 'male',
                'marital_status' => 'married',
                'blood_group'    => 'B+',
                'address'        => '12, Shanti Nagar, Ahmedabad, Gujarat - 380001',
                'joining_date'        => '2022-01-10',
                'department'          => 'Engineering',
                'designation'         => 'Senior Software Engineer',
                'employment_type'     => 'full-time',
                'work_location'       => 'Head Office',
                'probation_period'    => '3 months',
                'status'              => 'active',
                'bank_name'              => 'State Bank of India',
                'account_holder_name'    => 'Harsukh Patel',
                'bank_account_number'    => '32145678901234',
                'ifsc_code'              => 'SBIN0001234',
                'upi_id'                 => 'harsukh@sbi',
                'salary_payment_method'  => 'bank-transfer',
                'emergency_contact_name'     => 'Rekha Patel',
                'emergency_contact_relation' => 'Spouse',
                'emergency_contact_number'   => '+91 9876543210',
                'aadhar_number'  => '1234 5678 9012',
                'pan_number'     => 'ABCDE1234F',
                'device_type'      => 'Laptop',
                'device_brand'     => 'Dell',
                'device_model'     => 'Latitude 5520',
                'serial_number'    => 'DL5520XYZ001',
                'operating_system' => 'Windows 11 Pro',
                'ram'              => '16GB',
                'storage'          => '512GB SSD',
                'processor'        => 'Intel Core i7-11th Gen',
                'device_location'  => 'Head Office',
                'device_assigned_date'  => '2022-01-10',
                'device_condition'      => 'good',
                'company_asset_id'      => 'ASSET-0001',
                'installed_work_apps'   => "Slack\nZoom\nVS Code\nPostman",
                'device_login_email'    => 'harsukh@company.com',
                'antivirus_installed'   => true,
                'remote_access_enabled' => true,
                'additional_note'       => 'Core team member. Has access to production servers.',
            ],
            [
                'employee_id'    => 'EMP-0002',
                'name'           => 'Priya Sharma',
                'email'          => 'priya.sharma@company.com',
                'username'       => 'priya.sharma',
                'date_of_birth'  => '1998-03-22',
                'gender'         => 'female',
                'marital_status' => 'single',
                'blood_group'    => 'O+',
                'address'        => '45, Green Park Colony, Surat, Gujarat - 395001',
                'joining_date'        => '2023-04-01',
                'department'          => 'Human Resources',
                'designation'         => 'HR Executive',
                'employment_type'     => 'full-time',
                'work_location'       => 'Head Office',
                'probation_period'    => '3 months',
                'status'              => 'active',
                'bank_name'             => 'HDFC Bank',
                'account_holder_name'   => 'Priya Sharma',
                'bank_account_number'   => '50100456789012',
                'ifsc_code'             => 'HDFC0001567',
                'upi_id'                => 'priya@hdfc',
                'salary_payment_method' => 'bank-transfer',
                'emergency_contact_name'     => 'Mohan Sharma',
                'emergency_contact_relation' => 'Father',
                'emergency_contact_number'   => '+91 9823456701',
                'aadhar_number'  => '9876 5432 1098',
                'pan_number'     => 'FGHIJ5678K',
                'device_type'      => 'Laptop',
                'device_brand'     => 'HP',
                'device_model'     => 'EliteBook 840 G8',
                'serial_number'    => 'HP840XYZ002',
                'operating_system' => 'Windows 11 Home',
                'ram'              => '8GB',
                'storage'          => '256GB SSD',
                'processor'        => 'Intel Core i5-11th Gen',
                'device_location'  => 'Head Office',
                'device_assigned_date'  => '2023-04-01',
                'device_condition'      => 'new',
                'company_asset_id'      => 'ASSET-0002',
                'installed_work_apps'   => "Slack\nZoom\nMS Office",
                'device_login_email'    => 'priya.sharma@company.com',
                'antivirus_installed'   => true,
                'remote_access_enabled' => false,
                'additional_note'       => 'Handles onboarding and payroll coordination.',
            ],
            [
                'employee_id'    => 'EMP-0003',
                'name'           => 'Rahul Mehta',
                'email'          => 'rahul.mehta@company.com',
                'username'       => 'rahul.mehta',
                'date_of_birth'  => '2000-11-08',
                'gender'         => 'male',
                'marital_status' => 'single',
                'blood_group'    => 'A+',
                'address'        => '78, Vijay Nagar, Vadodara, Gujarat - 390001',
                'joining_date'        => '2024-07-15',
                'department'          => 'Engineering',
                'designation'         => 'Junior Developer',
                'employment_type'     => 'intern',
                'work_location'       => 'Remote',
                'probation_period'    => '6 months',
                'status'              => 'active',
                'bank_name'             => 'ICICI Bank',
                'account_holder_name'   => 'Rahul Mehta',
                'bank_account_number'   => '012345678901',
                'ifsc_code'             => 'ICIC0002345',
                'salary_payment_method' => 'bank-transfer',
                'emergency_contact_name'     => 'Suresh Mehta',
                'emergency_contact_relation' => 'Father',
                'emergency_contact_number'   => '+91 9712345678',
                'aadhar_number'  => '5555 6666 7777',
                'pan_number'     => 'LMNOP9012Q',
                'device_type'      => 'Laptop',
                'device_brand'     => 'Lenovo',
                'device_model'     => 'ThinkPad E14',
                'serial_number'    => 'LNV14XYZ003',
                'operating_system' => 'Ubuntu 22.04 LTS',
                'ram'              => '8GB',
                'storage'          => '512GB SSD',
                'processor'        => 'AMD Ryzen 5 5500U',
                'device_location'  => 'Remote',
                'device_assigned_date'  => '2024-07-15',
                'device_condition'      => 'new',
                'company_asset_id'      => 'ASSET-0003',
                'installed_work_apps'   => "Slack\nVS Code\nGit\nDocker",
                'device_login_email'    => 'rahul.mehta@company.com',
                'antivirus_installed'   => false,
                'remote_access_enabled' => true,
                'additional_note'       => 'Intern on 6-month contract. Working on frontend tasks.',
            ],
        ];

        // Set EMP-0001 as reporting manager for EMP-0002 and EMP-0003
        foreach ($employees as $data) {
            Employee::updateOrCreate(
                ['employee_id' => $data['employee_id']],
                $data
            );
        }

        $manager = Employee::where('employee_id', 'EMP-0001')->first();
        if ($manager) {
            Employee::whereIn('employee_id', ['EMP-0002', 'EMP-0003'])
                ->update(['reporting_manager_id' => $manager->id]);
        }

        $this->command->info('Seeded ' . count($employees) . ' employees.');
    }
}
