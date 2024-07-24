<?php

/**
 * View AI Attorney Solution
 * Version: 19JUN2024
 * @param int $step The current step in the onboarding process    
 */
function render_progress_bar($step) {
    // Ensure $step is an integer between 1 and 3
    $step = intval($step);
    if ($step < 1 || $step > 3) {
        $step = 1;
    }

    // Calculate the width of the progress bar
    $progress_width = ($step / 3) * 100;
    ?>

    <div class="owcpc_steps__container">
        <div class="container-fluid">
            <!-- <div class="row">
                <div class="col">
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress_width; ?>%;"
                            aria-valuenow="<?php echo $step; ?>" aria-valuemin="0" aria-valuemax="3"></div>
                    </div>
                </div>
            </div> -->
            

            <div class="row justify-content-between mt-3">
                <div class="col text-center px-3">
                    <div class="rounded-circle align-center bg-success text-white" style="width: 30px; height: 30px; line-height: 30px;">
                        ✓</div>
                    <p class="mt-2 owcp_color--grey">Step 1</p>
                    <h5>Fill the form</h5>
                    <p class="d-mobile-none">Fill out the intake form with details about your tenant issue.</p>
                </div>
                <div class="col text-center px-3">
                    <div class="rounded-circle <?php echo $step >= 2 ? 'bg-success text-white' : 'bg-light'; ?>"
                        style="width: 30px; height: 30px; line-height: 30px;">✓</div>
                    <p class="mt-2 owcp_color--grey">Step 2</p>
                    <h5>AI Analysis</h5>
                    <p class="d-mobile-none">Our AI will analyze your information and provide a recommended legal solution.</p>
                </div>
                <div class="col text-center px-3">
                    <div class="rounded-circle <?php echo $step == 3 ? 'bg-success text-white' : 'bg-light'; ?>"
                        style="width: 30px; height: 30px; line-height: 30px;">✓</div>
                    <p class="mt-2 owcp_color--grey">Step 3</p>
                    <h5>Review Your Solution</h5>
                    <p class="d-mobile-none">Schedule a call with an attorney to review and finalize your plan.</p>
                </div>
            </div>
        </div>
    </div>

    <?php
}
