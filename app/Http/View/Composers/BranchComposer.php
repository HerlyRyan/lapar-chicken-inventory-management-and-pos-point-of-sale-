<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Branch;

class BranchComposer
{
    public function compose(View $view)
    {
        // Pass-through values already shared by BranchContext to avoid mismatches
        $data = $view->getData();

        $selectedBranch = $data['selectedBranch'] ?? ($data['currentBranch'] ?? null);
        $branches = $data['branches'] ?? ($data['allBranches'] ?? Branch::where('is_active', true)->get());
        $showBranchSelector = $data['showBranchSelector'] ?? ($data['canSwitchBranch'] ?? false);

        $view->with([
            'selectedBranch' => $selectedBranch,
            'branches' => $branches,
            'showBranchSelector' => $showBranchSelector,
        ]);
    }
}
