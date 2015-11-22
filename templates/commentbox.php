<?php
class CommentBoxTemplate
{

	static function BeginCommentsBox($Templater)
	{
		$Templater->BeginTag('a', array('name' => 'comments'));
		$Templater->BeginTag('article', array('class' => 'boundedbox plugin show commentbox'));
	}
	
	static function AddCommentsPostingForm($Templater, $UniqueID, $FoundComments)
	{		
		$Templater->BeginTag('form', array('style' => 'text-align: center', 'action' => $_SERVER['PHP_SELF'] . '?id=' . $UniqueID, 'method' => 'POST'));
			$Templater->BeginTag('textarea', array('required' => 'required', 'style' => 'margin-top: 15px;', 'rows' => '10', 'cols' => '35', 'placeholder' => 'Comment on this plugin...', 'name' => 'Comment'));
			$Templater->EndLastTag();
			$Templater->BeginTag('br', array(), true);
			$Templater->BeginTag('input', array('name' => 'Submit', 'style' => 'margin: 10px 0px;', 'type' => 'Submit', 'value' => 'Submit comment'), true);
		$Templater->EndLastTag();

		if (!$FoundComments)
		{
			$Templater->BeginTag('hr', array(), true);
			$Templater->BeginTag('h3', array('style' => 'text-align: center;'));
				$Templater->Append('No comments yet; make some comments!');
			$Templater->EndLastTag();
		}
	}
	
	static function AddCommentsDisplay($Value, $AuthorDetails, $Templater)
	{
		$Templater->BeginTag('section'); // To provide a container to allow CSS to access the whole comment as a child, and hide its <hr> if it's the first
			$Templater->BeginTag('hr', array(), true);
			$Templater->BeginTag('p');
				list($DisplayName, $FullName, $ProfileImageURL) = $AuthorDetails;
				$Templater->BeginTag('img', array('class' => 'profileimage', 'style' => 'float: left; margin-right: 5px;', 'margin-top' => '8px', 'src' => $ProfileImageURL, 'alt' => 'Avatar of ' . $FullName, 'title' => 'Author of comment: ' . $FullName));
					$Templater->Append($Value);
				$Templater->EndLastTag();
			$Templater->EndLastTag();
		$Templater->EndLastTag();
	}
	
	static function EndCommentsBox($Templater)
	{
		$Templater->EndLastTag();
		$Templater->EndLastTag();
	}
}
?>