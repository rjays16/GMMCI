<?php /* Smarty version 2.6.0, created on 2018-04-24 13:09:04
         compiled from news/headline_newslist_item.tpl */ ?>

<div class="news_img"><img <?php echo $this->_tpl_vars['sHeadlineImg']; ?>
 align="left" border=0 hspace=10 <?php echo $this->_tpl_vars['sImgWidth']; ?>
></div>
<?php echo $this->_tpl_vars['sHeadlineItemTitle']; ?>


<?php if ($this->_tpl_vars['sPreface']): ?>
	<br/>
	<?php echo $this->_tpl_vars['sPreface']; ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['sNewsPreview']): ?>
	<p></p>
	<?php echo $this->_tpl_vars['sNewsPreview']; ?>

<?php endif; ?>

<br>
<?php echo $this->_tpl_vars['sEditorLink']; ?>
	