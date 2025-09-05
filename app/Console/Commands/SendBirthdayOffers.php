<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\BirthdayOfferMail;
use Symfony\Component\Console\Command\Command as SymfonyCommand; // <-- add

class SendBirthdayOffers extends Command
{
    protected $signature = 'offers:send-birthday';
    protected $description = 'Send birthday offers to users';

    public function handle(): int
    {
        User::with('profile')
            ->get()
            ->filter(fn ($u) => $u->profile && $u->profile->isBirthday())
            ->each(fn ($u) => Mail::to($u)->queue(new BirthdayOfferMail($u)));

        return SymfonyCommand::SUCCESS; // <-- use Symfony constant
    }
}