<?php

namespace App\Jobs;

use App\Mail\UserAddedMail;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Send mail when user registered
 *
 * Class UserSuccessfulAddedToActivityJob
 * @package App\Jobs
 */
class UserSuccessfulAddedToActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Activity $activity
     */
    protected $activity;

    /**
     * @var User $user
     */
    public $user;

    /**
     * Create a new job instance.
     *
     * @param Activity $activity
     * @param User $user
     *
     * @return void
     */
    public function __construct(Activity $activity, User $user)
    {
        $this->activity = $activity;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->send(new UserAddedMail($this->activity));
    }
}
