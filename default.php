<?php if (!defined('APPLICATION')) exit();

// Define the plugin
$PluginInfo['BigCount'] = array(
	'Name' => 'Big Count',
	'Description' => 'This gives big counts for follows, comments, and views. It is a fork of the Voting plugin.',
	'Version' => '1.0b',
	'Author' => "Kit La Touche",
	'AuthorEmail' => 'kit@transneptune.net',
	'AuthorUrl' => 'http://transneptune.net'
);

class BigCountPlugin extends Gdn_Plugin {
	/**
	 * Add JS & CSS to the page.
	 */
	public function AddJsCss($Sender) {
		$Sender->AddCSSFile('bigcount.css', 'plugins/BigCount');
	}
	public function DiscussionsController_Render_Before($Sender) {
		$this->AddJsCss($Sender);
	}
	public function CategoriesController_Render_Before($Sender) {
		$this->AddJsCss($Sender);
	}

	/**
	 * Add the "Stats" buttons to the discussion list.
	 */
	public function Base_BeforeDiscussionContent_Handler($Sender) {
		$Session = Gdn::Session();
		$Discussion = GetValue('Discussion', $Sender->EventArguments);
		// Comments
		$Css = 'StatBox CommentsBox';
		if ($Discussion->CountComments > 1)
			$Css .= ' HasCommentsBox';

		if (!is_numeric($Discussion->CountBookmarks))
			$Discussion->CountBookmarks = 0;
			
		echo Wrap(Wrap(T('Comments')) . Gdn_Format::BigNumber($Discussion->CountComments - 1), 
				  'div', array('class' => $Css));
		
		// Views
		echo Wrap(Wrap(T('Views')) . Gdn_Format::BigNumber($Discussion->CountViews),
				  'div', array('class' => 'StatBox ViewsBox'));
	
		// Follows
		$Title = T($Discussion->Bookmarked == '1' ? 'Unbookmark' : 'Bookmark');
		if ($Session->IsValid()) {
			echo Wrap(Anchor(
				Wrap(T('Follows')) . Gdn_Format::BigNumber($Discussion->CountBookmarks),
				'/vanilla/discussion/bookmark/'.$Discussion->DiscussionID.'/'.$Session->TransientKey().'?Target='.urlencode($Sender->SelfUrl),
				'',
				array('title' => $Title)
			), 'div', array('class' => 'StatBox FollowsBox'));
		} else {
			echo Wrap(Wrap(T('Follows')) . $Discussion->CountBookmarks,
					  'div', array('class' => 'StatBox FollowsBox'));
		}	
	}
}
?>