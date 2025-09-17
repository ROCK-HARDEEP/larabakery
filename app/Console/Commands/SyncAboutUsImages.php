<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AboutUs;
use App\Models\PageSection;

class SyncAboutUsImages extends Command
{
    protected $signature = 'sync:about-images';
    protected $description = 'Sync existing AboutUs images to PageSection model';

    public function handle()
    {
        $this->info('Starting sync of AboutUs images to PageSection...');
        
        $aboutUs = AboutUs::first();
        
        if (!$aboutUs) {
            $this->warn('No AboutUs record found.');
            return;
        }
        
        // Sync story image
        if ($aboutUs->story_image) {
            $section = PageSection::where('page', 'about')
                ->where('section', 'our_story')
                ->first();
                
            if ($section && !$section->image) {
                $section->image = $aboutUs->story_image;
                $section->save();
                $this->info('Synced story image: ' . $aboutUs->story_image);
            }
        }
        
        // Sync team member images
        if ($aboutUs->team_members) {
            $section = PageSection::where('page', 'about')
                ->where('section', 'meet_our_team')
                ->first();
                
            if ($section) {
                $data = $section->data ?? [];
                $members = $data['members'] ?? [];
                $updated = false;
                
                foreach ($aboutUs->team_members as $index => $teamMember) {
                    if (isset($teamMember['image']) && $teamMember['image']) {
                        if (isset($members[$index])) {
                            $members[$index]['image'] = $teamMember['image'];
                            $updated = true;
                        }
                    }
                }
                
                if ($updated) {
                    $data['members'] = $members;
                    $section->data = $data;
                    $section->save();
                    $this->info('Synced team member images');
                }
            }
        }
        
        $this->info('Image sync completed!');
    }
}