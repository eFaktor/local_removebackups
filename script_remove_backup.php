<?php
/**
 * - Script to remove the 'automated' backup from the mdl_files and filedir repository.
 *
 * @package         local
 * @copyright       2013 eFaktor    {@link http://www.efaktor.no}
 *
 * @creationDate    05/11/2013
 * @author          eFaktor     (fbv)
 *
 */

require_once('../config.php');

require_login();

/* Start the page */
$site_context = context_system::instance();

$PAGE->set_context($site_context);

$PAGE->set_url('/local/script_remove_backup.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);

/* Print Header */
echo $OUTPUT->header();

$sql = " SELECT   *
         FROM     {files}
         WHERE	  component = 'backup'
            AND	  filename != '.'
            AND	  filearea	= 'automated'
         LIMIT    50 ";

$rdo = $DB->get_records_sql($sql);
if ($rdo) {
    foreach ($rdo as $instance) {
        $content_hash = $instance->contenthash;

        try {
            $c1                 = $content_hash[0].$content_hash[1];
            $c2                 = $content_hash[2].$content_hash[3];
            $file_contenthash   = $CFG->dataroot .'/filedir' . '/' . $c1 . '/' . $c2 . '/' . $content_hash;
            unlink($file_contenthash);
            $file_contenthash   = $CFG->dataroot .'/filedir' . '/' . $c1 . '/' . $c2 . '/';
            if (is_dir($file_contenthash)) {
                rmdir($file_contenthash);
            }//if_is_dir

            $file_contenthash   = $CFG->dataroot .'/filedir' . '/' . $c1 . '/';
            if (is_dir($file_contenthash)) {
                rmdir($file_contenthash);
            }//if_is_dir

            echo "Remove Content Hash: " . $content_hash . '</br>';

            $fs         = get_file_storage();
            $file_store = $fs->get_file_instance($instance);
            $file_store->delete();
        }catch (Exception $ex) {
            echo "Not Remove Content Hash: " . $content_hash . '</br>';
        }//try_catch
    }//for_rdo


}else {
    echo 'There are no more autobackup files to remove';
}//if_rdo

echo $OUTPUT->notification(get_string('continue'), 'notifysuccess');
echo $OUTPUT->continue_button($CFG->wwwroot);

/* Print Footer */
echo $OUTPUT->footer();
