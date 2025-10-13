<?php

namespace App\Models;

class SemiFinishedUsageRequestOutput extends SemiFinishedUsageRequestTarget
{
    // Explicitly ensure table name (inherits relations and fillables from Target for reuse)
    protected $table = 'semi_finished_usage_request_outputs';
}
