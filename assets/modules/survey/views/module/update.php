<?php defined('MODX_BASE_PATH') or die('Error'); ?>
<?php /** @var Surveys $app */ ?>
<?php /** @var Survey $survey */ ?>
<form method="post" action="index.php?a=112&id=<?= $id ?>&action=<?php echo $survey->id ? 'update' : 'create' ?>" onsubmit="return Survey.<?php echo $survey->id ? 'update' : 'create' ?>(this)">
    <input type="hidden" name="survey" value="<?= $survey->id ?>"/>

    <table class="fields survey">
        <tr>
            <td>
                <div>
                <label for="title"><?= $app->t('title') ?></label>
                <input required id="title" type="text" name="title" value="<?= e($survey->title) ?>"/>
                </div>

                <div>
                <label for="description"><?= $app->t('description') ?></label>
                <textarea name="description" id="description" cols="30" rows="10"><?= e($survey->description) ?></textarea>
                </div>

                <div>
                <label for="active"><input id="active" type="checkbox" name="active" value="1" <?php echo $survey->active ? 'checked' : '' ?>/> <?= $app->t('published') ?></label>
                </div>
            </td>
            <td>
                <div class="options">
                    <label><?= $app->t('options') ?></label>
                    <ul id="options">
                        <?php if ($survey->options): ?>
                        <?php foreach($survey->options as $o): ?>
                            <li><input required type="text" name="option[id_<?= $o->id ?>]" value="<?= $o->title ?>"/><input type="hidden" name="option_sort[id_<?= $o->id ?>]" value="<?= $o->sort ?>" /><span class="remove-ico" onclick="Survey.removeOption(this)"></span><span class="move-ico"></span></li>
                        <?php endforeach ?>
                        <?php endif ?>
                        <?php if (!$survey->id): ?>
                            <li><input required type="text" name="new_option[]" value=""/><input type="hidden" name="new_option_sort[]" value="0" /><span class="remove-ico" onclick="Survey.removeOption(this)"></span><span class="move-ico"></span></li>
                        <?php endif ?>
                    </ul>
                    <div class="button_add"><button type="button" onclick="Survey.addOption(this)"><img alt="" src="media/style/<?= $modx->config['manager_theme'] ?>/images/icons/add.png"> <?= $app->t('add_option') ?></button></div>
                </div>
            </td>
        </tr>
    </table>

    <div class="buttons">
        <button type="submit"><img alt="icons_resource_duplicate" src="media/style/<?= $modx->config['manager_theme'] ?>/images/icons/save.png"> <?= $app->t('save') ?></button>
    </div>
</form>
