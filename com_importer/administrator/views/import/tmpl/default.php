<?php
/**
 * @version     1.0.0
 * @package     com_teammanager
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Pieter <joomla@jort.net> - http://
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// Import CSS
$document = &JFactory::getDocument();
$document->addStyleSheet('components/com_teammanager/assets/css/teammanager.css');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'import.cancel' || document.formvalidator.isValid(document.id('import-form'))) {
			Joomla.submitform(task, document.getElementById('import-form'));
		}
		else 
		{
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_teammanager&view=import'); ?>" method="post" name="adminForm" id="import-form" class="form-validate" enctype="multipart/form-data">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_TEAMMANAGER_LEGEND_IMPORT'); ?></legend>
			<ul class="adminformlist">
				<li>
					<?php echo $this->form->getLabel('importfile'); ?>
					<?php echo $this->form->getInput('importfile'); ?>
				</li>
            </ul>
		</fieldset>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"/>

    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>