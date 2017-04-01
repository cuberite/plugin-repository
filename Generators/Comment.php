<?php
require_once '../composer/vendor/autoload.php';

require_once 'Globals.php';
require_once 'Environment Interfaces/meekrodb.php';
require_once 'Environment Interfaces/Cache.php';

final class Comment
{
	public $Author;
	public $Text;
	
	public function __construct($Details)
	{
		$this->Author = unserialize(Cache::GetCacheEntry(CacheType::Users, $Details['AuthorID']));
		$this->Text = $Details['Comment'];
	}
}

final class CommentModuleGenerator
{
	public static function GenerateAndCache($RepositoryID)
	{
		$Templater = new Twig_Environment(new Twig_Loader_Filesystem('Templates/Modules'));
		$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);
		
		$Response = $SQLLink->query('SELECT * FROM Comments WHERE LinkedRepositoryID = %i', $RepositoryID);
		foreach ($Response as $Comment)
		{
			$Directory = Cache::GetCacheDir() . DIRECTORY_SEPARATOR . CacheType::Comments . DIRECTORY_SEPARATOR . $RepositoryID;
			if (!is_dir($Directory))
			{
				mkdir($Directory);
			}
			
			Cache::UpdateCacheEntry(
				CacheType::Comments,
				$RepositoryID . DIRECTORY_SEPARATOR . $Comment['UniqueID'] . '.html',
				$Templater->render('Comment.html', array('Comment' => new Comment($Comment)))
			);
		}
	}
}
?>