<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organizations = Organization::whereOrganizationTypeId(NULL)->get();

        foreach($organizations as $organization) {
            $organization->organization_type_id = 1;
            $organization->save();
        }
    }
}
