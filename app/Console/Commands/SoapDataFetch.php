<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class SoapDataFetch extends Command
{
    // Set the command signature
    protected $signature = 'soap:fetch'; // Set a meaningful name here
    protected $description = 'Fetch data from a SOAP web service';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Your command logic here
    }
}

