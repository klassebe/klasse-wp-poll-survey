<?php
require_once __DIR__ . '/../classes/post-types/test-modus.php';
$testmodi = \kwps_classes\Test_Modus::get_published_modi();
?>

<div class="wrap">
    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php _e(get_admin_page_title()) ?></h2>
    <form id="create-new-test" method="post" action="?page=<?php echo $_REQUEST['page'];?>&action=add_test_collection&noheader=true">
        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="post_title">Titel van de test</label>
                </th>
                <td>
                    <input name="post_title" id="post_title" class="regular-text"><span class="help-block hidden"></span>
                    <p class="description">Dit wordt de titel van uw test.</p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="kwpsTestModi">Testmodus</label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Testmodi</span>
                        </legend>

                        <?php foreach($testmodi as $testmodus): ?>
                        <label for="kwpsTestModi_<?php echo $testmodus['ID'];?>">
                            <input type="radio" value="<?php echo $testmodus['ID'];?>" name="post_parent" id="kwpsTestModi_<?php echo $testmodus['ID'];?>">
                            <span><?php echo $testmodus['post_title'];?></span>
                            <p><?php echo $testmodus['post_content'];?></p>
                        </label><br>
                        <?php endforeach; ?>

                    </fieldset>
                    <span class="help-block hidden"></span>
                    <p class="description">Kies het type van de test die je wilt maken.</p>
                </td>
            </tr>
            </tbody>
        </table>
        <p>
            <input type="hidden" name="type" value="kwps_test_collection">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'];?>">
        </p>
        <p class="submit">
            <button type="submit" class="button button-primary">Maak</button>
        </p>
    </form>
</div> <!-- .wrap -->