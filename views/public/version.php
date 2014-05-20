<div>
<?php if(!empty($data['intro'])): ?>
	<div id="kwps-intro" class="kwps-page intro">
		<div class="kwps-content">
			<?php echo $data['intro']['post_content']; ?>
		</div>
		<div class="kwps-button">
			<button class="kwps-next">Next</button>
		</div>
	</div>
<?php endif; ?>


<?php foreach($data['question_groups'] as $questionGroup): ?>
<div class="kwps-page question_group">
	<div>
		<?php echo $questionGroup['post_title']; ?>
	</div>
	<div class="kwps-questions">
	<?php foreach($questionGroup['questions'] as $question): ?>
	<div class="kwps-question">
		<?php echo $question['post_content'] ?>
		<div class="kwps-answer-option">
		<ul>
		<?php foreach($question['answer_options'] as $answerOption): ?>
			<li><input id="answer-option-<?php echo $answerOption['ID'] ?>" type="radio" name="answer_option[]" value="<?php echo $answerOption['ID'] ?>"><label for="answer-option-<?php echo $answerOption['ID'] ?>"><?php echo $answerOption['post_content'] ?></label></li>
		<?php endforeach; ?>
		</ul>
		</div>
	</div>
	<div class="kwps-button">
		<button class="kwps-next">Next</button>
	</div>
	<?php endforeach; ?>
	</div>
</div>
<?php endforeach; ?>

<?php if(!empty($data['outro'])): ?>
	<div id="kwps-outro" class="kwps-page outro">
		<div class="kwps-content">
			<?php echo $data['outro']['post_content']; ?>
		</div>
	</div>
<?php endif; ?>
</div>