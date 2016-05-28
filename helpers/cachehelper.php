<?php
final class RepositoryResourcesCache
{
	const CACHE_TYPE_USERDATA = 'Users';
	const CACHE_TYPE_REPOSITORYDATA = 'Repositories';
	
	static private function GetCacheDir()
	{
		return getcwd() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'GitHub Cache';
	}
	
	static public function UpdateCacheEntries($CacheType, $RepositoryID, $EntryName, $EntryData)
	{
		@mkdir(RepositoryResourcesCache::GetCacheDir() . DIRECTORY_SEPARATOR . $CacheType . DIRECTORY_SEPARATOR . $RepositoryID);
		
		$FileName = RepositoryResourcesCache::GetCacheDir() . DIRECTORY_SEPARATOR . $CacheType . DIRECTORY_SEPARATOR . $RepositoryID . DIRECTORY_SEPARATOR . $EntryName;			
		if (empty($EntryData))
		{
			unlink($FileName);
		}
		else
		{
			file_put_contents($FileName, $EntryData, LOCK_EX);
		}
	}
	
	static public function GetCacheEntry($CacheType, $RepositoryID, $EntryName)
	{
		return @file_get_contents(RepositoryResourcesCache::GetCacheDir() . DIRECTORY_SEPARATOR . $CacheType . DIRECTORY_SEPARATOR . $RepositoryID . DIRECTORY_SEPARATOR . $EntryName);
	}
	
	static public function DeleteCache($CacheType, $RepositoryID)
	{
		$CacheDirectory = RepositoryResourcesCache::GetCacheDir() . DIRECTORY_SEPARATOR . $CacheType . DIRECTORY_SEPARATOR . $RepositoryID;
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
	}
}
?>