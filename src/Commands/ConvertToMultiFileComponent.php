<?php

namespace LivewireV4\Commands;

use App\Models\User;
use App\Support\DripEmailer;
use Illuminate\Console\Command;

class ConvertToMultiFileComponent extends Command
{

    protected $signature = 'convert-class-to:mfc {path}';


    protected $description = 'Convert Livewire class based component to Multi File Component(MFC)';


    public function handle(): void
    {
        dd($this->arguments());
    }
}