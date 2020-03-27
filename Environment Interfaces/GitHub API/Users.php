<?php namespace GitHubAPI;
require_once 'Base.php';

final class Users
{
	use GitHubAPIProvider;

	public static function PurgeOldUpdateHooks()
	{
		require_once 'Environment Interfaces/GitHub API/Repositories.php';

		$Repositories = Repositories::GetInstance()->getReceiver(\FlexyProject\GitHub\Client::REPOSITORIES)->listYourRepositories();
		foreach ($Repositories as $Repository)
		{
			try
			{
				$Hooks = Users::CustomRequest('repos', $Repository['full_name'], 'hooks');
			}
			catch (\Exception $e)
			{
				continue;
			}

			foreach ($Hooks as $Hook)
			{
				if ($Hook['config']['url'] === "https://cuberiteplugins.azurewebsites.net/processhook")
				{
					Repositories::DeleteUpdateHook($Repository['id'], $Hook['id']);
				}
			}
		}
	}

	static function GetDetailsFromID($UserID)
	{
		$Profile = Users::CustomRequest('user', $UserID);
		return $Profile;
	}
}
?>