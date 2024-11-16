<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Position;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://randomuser.me/api/1.4?nat=ua&results=15&inc=name,email,phone,picture',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
          ));

          
        $response = curl_exec($curl);

        curl_close($curl);

        $positions_ids = Position::pluck('id');

        $response = json_decode($response);

        $api_result = collect($response)->first();

        $users = [];

        foreach($api_result as $item)
        {
            $phone = $item->phone;
            $phone = preg_replace('/\D/', '', $phone); 
            $phone = '+38' . $phone;


            $users[] = [
                'name' => $item->name->first .' ' . $item->name->last,
                'email' => $item->email,
                'photo' => $item->picture->medium,
                'phone' => $phone,
                'position_id' => $positions_ids->random(),
            ];
        }

        User::insert($users);
    }
}
