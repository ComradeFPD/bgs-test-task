<?php

namespace App\Mail;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Activity $activity
     */
   public $activity;

    /**
     * Create a new message instance.
     *
     * @param Activity $activity
     *
     * @return void
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.user-added', [
            'activity' => $this->activity
        ]);
    }
}
