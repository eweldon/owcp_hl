<?php
/**
 * View Application Page
 * Version: 8JUN2024
 */

global $client_data;
global $record_id;
global $portal;

// get auto-login-key
$record_id = $client_data->get("auto-login-key", "");

?>

<div class="wc">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4">Welcome to your</br><b>Tentant Defense Portal</b></h1>
                <p>We’re here to help you navigate your tenant issues quickly and efficiently.
                    Let’s get started by gathering some basic information about your situation.</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="px-4"><img src="<?php echo plugin_dir_url(dirname(__FILE__)) .'assets/images/ai-image.png'; ?>" alt="" class="img-fluid"></div>
            </div>
        </div>
        <div class="my-5">
            <h1 class="text-center pt-4 display-4">Here’s how the process works</h1>
            <?php render_progress_bar(1) ?>
        </div>

        <div class="row my-5 owcp_page-title__container">
            <h1 class="text-center mb-4 pt-4 display-4">Start now by completing step 1</h1>
            <div class="col-12">
                <?php
                // embed gravity form "id 1" with field values passing in record id as "record_id" and "auto-login-key" with ajax enabled
                echo do_shortcode('[gravityform id=' . OWCP_HA_AI_TENANT_FORM_ID . ' title=false description=false ajax=true field_values="record_id=' . $record_id . '&auto-login-key=' . $record_id . '"]');
                ?>
    </div>
    </div>

</div>
</div>