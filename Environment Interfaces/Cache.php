<?php
final class CacheType
{
	const Preprocessed = 'Preprocessed';
	const CondensedPlugins = 'Condensed Plugins';
	const ExpandedPlugins = 'Expanded Plugins';
	const Comments = 'Comments';
	const Users = 'Users';
}

final class Cache
{
	static public function GetCacheDir()
	{
		return getcwd() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Static Cache';
	}

	static public function UpdateCacheEntry($CacheType, $EntryID, $EntryData)
	{
		$FileName = Cache::GetCacheDir() . DIRECTORY_SEPARATOR . $CacheType . DIRECTORY_SEPARATOR . $EntryID;
		file_put_contents($FileName, $EntryData, LOCK_EX);
	}

	static public function GetCacheEntry($CacheType, $EntryID)
	{
		return @file_get_contents(Cache::GetCacheDir() . DIRECTORY_SEPARATOR . $CacheType . DIRECTORY_SEPARATOR . $EntryID);
	}

	static public function DeleteCache($CacheType, $EntryID)
	{
		$CacheDirectory = Cache::GetCacheDir() . DIRECTORY_SEPARATOR . $CacheType . DIRECTORY_SEPARATOR . $EntryID;
		unlink($CacheDirectory);

		/* Just in case we ever need it again
		foreach (
			new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($CacheDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
				RecursiveIteratorIterator::CHILD_FIRST
			) as $Entry)
		{
			if ($Entry->isDir())
			{
				rmdir($Entry->getRealPath());
			}
			else
			{
				unlink($Entry->getRealPath());
			}
		}

		rmdir($CacheDirectory);
		*/
	}
}
?>