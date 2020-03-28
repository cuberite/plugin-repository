<?php namespace GitHubAPI;
require_once 'Base.php';

final class Users
{
	use GitHubAPIProvider;

	public static function PurgeOldUpdateHooks()
	{
		require_once 'Models/Plugin.php';
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
				$Config = $Hook['config'];
				if (isset($Config['url']) && ($Config['url'] === "https://cuberiteplugins.azurewebsites.net/processhook"))
				{
					Repositories::DeleteUpdateHook($Repository['id'], $Hook['id']);
					$HookId = Repositories::CreateUpdateHook($Repository['id']);
					\PluginGenerator::UpdateWebhook($Repository['id'], $HookId); // TODO: check actually updated
					error_log("Refreshed hook for " . $Repository['full_name'] . "\r\n", 3, '../refresh.log');
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