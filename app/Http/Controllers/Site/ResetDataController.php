<?php
/**
 * @package ResetDataController
 * @author TechVillage <support@techvill.org>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 * @created 19-07-2023
 */

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResetDataController extends Controller
{
    /**
     * Reset data from database
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        if (!config('openAI.is_demo')) {
            abort(404);
        }

        if (!$request->filled('code')) {
            return redirect()->route('frontend.index')->withFail(__('Code is required.'));
        }

        if ($request->code <> techDecrypt('FUElHpVQ+FQGZxznFf2XoJIOe3GChUTX')) {
            return redirect()->route('frontend.index')->withFail(__('Your code is invalid.'));
        }

        ini_set('max_execution_time', 600);

        \Artisan::call('down');

        sleep(5);
        try {
            if ($request->filled('dummy_data') && $request->dummy_data == 'false') {
                \Artisan::call('app:install --dummydata=false');
            } else {
                \Artisan::call('app:install');
            }
        } catch (\Exception $e) {
            \Artisan::call('up');

            return redirect()->route('frontend.index')->withFail($e->getMessage());
        }

        \Artisan::call('up');

        return redirect()->route('frontend.index')->withSuccess(__('Database reset successfully.'));
    }
}
