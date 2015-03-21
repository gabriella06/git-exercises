<?php

class GitUtils
{
    private function __construct()
    {
    }

    /**
     * @param $commitId
     * @return string
     */
    public static function getCommiterName($commitId)
    {
        exec("git log --pretty=format:\"%cn\" -1 $commitId", $commiter);
        return $commiter[0];
    }

    /**
     * @param $commitId
     * @return array
     */
    public static function getChangedFilenames($commitId)
    {
        exec("git diff-tree --no-commit-id --name-only -r $commitId", $changedFiles);
        return $changedFiles;
    }

    /**
     * @param $commitIdLeft
     * @param $commitIdRight
     * @return array
     */
    public static function getCommitIdsBetween($commitIdLeft, $commitIdRight)
    {
        exec("git show --format=format:%H --quiet $commitIdLeft...$commitIdRight", $commits);
        return $commits;
    }

    /**
     * Check if given filename would be ignored in the specified commit id by any of the commited .gitignore files.
     * @param $commitId
     * @param $filename
     * @return false if the file is not ignored or array with 3 keys when the file is ignored
     *          0 = path .gitignore file that contains matching rule
     *          1 = line in the file that contains the rule
     *          2 = rule
     */
    public static function checkIgnore($commitId, $filename)
    {
        exec("git checkout -q $commitId");
        exec("git check-ignore -v $filename", $result, $status);
        exec("git checkout -q master");
        if ($status) {
            return false;
        }
        $result = explode(':', $result[0]);
        $result[2] = current(explode("\t", $result[2]));
        return $result;
    }

    /**
     * @param $commitId
     * @param $filename
     * @return array file content as lines
     */
    public static function getFileContent($commitId, $filename)
    {
        exec("git show $commitId:$filename", $fileLines);
        return $fileLines;
    }
}
