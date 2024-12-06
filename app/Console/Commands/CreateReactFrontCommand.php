<?php

namespace App\Console\Commands;

use App\Http\Services\Codebuilder\LayoutService;
use Illuminate\Console\Command;

use Exception;

class CreateReactFrontCommand extends Command
{
    protected $signature = 'front:react 
                        {module : Name of the Module} 
                        {table : Name of the table} 
                        {label : The label for the table}';

    protected $description = 'Create or edit the front-end react file with the information provided as parameters';

    protected $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        parent::__construct();

        $this->layoutService = $layoutService;
    }

    public function handle()
    {
        $module = $this->arguments()['module'];
        $table = $this->arguments()['table'];
        $label = $this->argument()['label'];

        // Generate the Grid Layout
        $gridFile = $table . '_grid.json';

        $result = $this->layoutService->generateGridParentLayout($module, $table, $label, $gridFile);
        if (isset($result) and count($result) > 1) {
            $this->info($result[1] . ' ' . $result[2] . ' ' . $result[3]);
        }
        // Generate the Form Layout
        $formFile = $table . '_form.json';
        $result = $this->layoutService->generateFormParentLayout($module, $table, $label, $formFile);
        if (isset($result) and count($result) > 1) {
            $this->info($result[1] . ' ' . $result[2] . ' ' . $result[3]);
        }
        return 0;
    }
}
