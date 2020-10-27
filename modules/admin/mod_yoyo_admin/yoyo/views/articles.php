<ul style="margin: 10px 0; padding-left: 20px;">
	
	<?php if (! $this->articles): ?>
		<li>No results</li>
	<?php endif; ?>

	<?php foreach ($this->articles as $article): ?>
		<li>
			<?php if ($article->link): ?>
			<a href="<?php echo $article->link; ?>">
				<?php echo $article->title; ?>
			</a>
			<?php else: ?>
				<?php echo $article->title; ?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>

</ul>

<div style="margin-top: 10px">

	<button 
		<?php echo !$this->previous ? 'disabled' : ''; ?>
		yoyo:vars="page: <?php echo $this->previous; ?>"
	>
		Previous
	</button>

	<button 
		yoyo:vars="page: <?php echo $this->next; ?>"
	>
		Next
	</button>

</div>
