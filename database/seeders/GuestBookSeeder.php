<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\GuestBook;
use Illuminate\Database\Seeder;

class GuestBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::query()->where('status', 'active')->pluck('id', 'name');

        if ($companies->isEmpty()) {
            return;
        }

        $messages = [
            ['guest_name' => 'Alicia Hart', 'company' => 'Northstar Labs', 'message' => 'This event feels energizing and well organized. Thank you for creating such a warm and inspiring atmosphere.'],
            ['guest_name' => 'Brandon Lee', 'company' => 'BluePeak Studio', 'message' => 'The guest book experience is beautiful and thoughtful. It is a great way to capture the spirit of the evening.'],
            ['guest_name' => 'Cynthia Moore', 'company' => 'SummitWorks', 'message' => 'A wonderful evening full of connection, creativity, and great energy. We are glad to be part of it.'],
            ['guest_name' => 'Daniel Brooks', 'company' => 'Northstar Labs', 'message' => 'Amazing work by the team. The display wall and registration experience really elevated the whole event.'],
            ['guest_name' => 'Evelyn Chen', 'company' => 'BluePeak Studio', 'message' => 'Everything feels polished and welcoming. It is exciting to see community and business come together like this.'],
            ['guest_name' => 'Farah Ahmed', 'company' => 'SummitWorks', 'message' => 'Great hospitality, great vibe, and a memorable event. We are grateful to be part of such a thoughtful gathering.'],
            ['guest_name' => 'George Patel', 'company' => 'Northstar Labs', 'message' => 'This is one of the best networking experiences we have attended. The attention to detail is excellent.'],
            ['guest_name' => 'Hannah Morris', 'company' => 'BluePeak Studio', 'message' => 'The event setup is incredibly welcoming and beautifully designed. We loved the guest book moments.'],
            ['guest_name' => 'Isaac Rivera', 'company' => 'SummitWorks', 'message' => 'Excellent planning and a fantastic environment. The guest wall is a great addition to the event experience.'],
            ['guest_name' => 'Julia Nguyen', 'company' => 'Northstar Labs', 'message' => 'The energy in the room is amazing. Thank you for putting together a meaningful and productive event.'],
            ['guest_name' => 'Kevin Collins', 'company' => 'BluePeak Studio', 'message' => 'Wonderful design, thoughtful details, and warm hospitality. We enjoyed every part of the program.'],
            ['guest_name' => 'Lina Foster', 'company' => 'SummitWorks', 'message' => 'Very impressed with the experience. It is both personal and professional, which makes it memorable.'],
            ['guest_name' => 'Marcus White', 'company' => 'Northstar Labs', 'message' => 'A beautifully executed event with a great balance of energy, collaboration, and heartfelt moments.'],
            ['guest_name' => 'Nora Singh', 'company' => 'BluePeak Studio', 'message' => 'We truly appreciated the thoughtfulness behind every detail. It created a warm and memorable experience for everyone.'],
            ['guest_name' => 'Oliver Perez', 'company' => 'SummitWorks', 'message' => 'The experience feels premium and inclusive. Thank you for bringing everyone together in such a meaningful way.'],
            ['guest_name' => 'Priya Raman', 'company' => 'Northstar Labs', 'message' => 'Everything was seamless from start to finish. We are grateful for the invitation and the amazing atmosphere.'],
            ['guest_name' => 'Quentin Ross', 'company' => 'BluePeak Studio', 'message' => 'A great event with strong energy and a polished guest book experience. We look forward to the next one.'],
            ['guest_name' => 'Rachel Kim', 'company' => 'SummitWorks', 'message' => 'The event provided a wonderful space to connect. We loved the thoughtful touches throughout the evening.'],
            ['guest_name' => 'Samuel Young', 'company' => 'Northstar Labs', 'message' => 'We appreciate the invitation and the care that went into every detail. It was a memorable and uplifting experience.'],
            ['guest_name' => 'Tara Wilson', 'company' => 'BluePeak Studio', 'message' => 'So much joy, purpose, and creative energy in one room. This is exactly the kind of event we love to support.'],
        ];

        foreach ($messages as $index => $entry) {
            $companyId = $companies[$entry['company']] ?? $companies->first();

            GuestBook::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'guest_name' => $entry['guest_name'],
                    'message' => $entry['message'],
                ],
                [
                    'display_status' => 'displayed',
                    'created_at' => now()->subDays(1)->addMinutes($index * 7),
                    'updated_at' => now()->subDays(1)->addMinutes($index * 7),
                ]
            );
        }
    }
}
