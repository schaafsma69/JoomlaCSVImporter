<?php
/**
 * @version     1.0.0
 * @package     com_importer
 * @copyright   Copyright (C) 2015. Alle rechten voorbehouden.
 * @license     GNU General Public License versie 2 of hoger; Zie LICENSE.txt
 * @author      Pieter <schaafsma69@gmail.com> - http://joomla.s-core.nl
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_importer/assets/css/importer.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {
        
    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'task.cancel') {
            Joomla.submitform(task, document.getElementById('task-form'));
        }
        else {
            
            if (task != 'task.cancel' && document.formvalidator.isValid(document.id('task-form'))) {
                
                Joomla.submitform(task, document.getElementById('task-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_importer&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="task-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_IMPORTER_TITLE_TASK', true)); ?>
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">
					<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('definition'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('definition'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('last_run'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('last_run'); ?></div>
					</div>
                </fieldset>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>