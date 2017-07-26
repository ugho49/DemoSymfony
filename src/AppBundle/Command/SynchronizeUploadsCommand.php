<?php

namespace AppBundle\Command;

use AppBundle\Entity\File;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizeUploadsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('synchronize:uploads')
            ->setDescription("This command synchronize 'uploads' folder in server with 'file' table in database")
            ->addOption('dry-count', "--dry-count", InputOption::VALUE_NONE, 'Not delete or add anything, only count the number of unused uploads')
            ->addOption('dry-run', "--dry-run", InputOption::VALUE_NONE, 'Not delete or add anything, only show the insertions and the deletions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Empty arrays
        $filesToDelete = []; //Files in database but not in server
        $filesToAdd = []; // Files in server but not in database

        // Entity Manager
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Upload dir
        $uploadDir = $this->getContainer()->get('kernel')->getRootDir() . '/../web/uploads';
        // For windows servers
        $uploadDir = str_replace("/", DIRECTORY_SEPARATOR, $uploadDir);

        // Get files in database
        /** @var File[] $filesInDatabase */
        $filesInDatabase = $em->getRepository(File::class)->findAll();
        $output->writeln(count($filesInDatabase) . " files found in database");

        // Create a map for files in database
        $mapFilesInDatabase = [];
        foreach ($filesInDatabase as $file) {
            $mapFilesInDatabase[$file->getName()] = $file;
        }

        // Scan Upload folder
        $filesInUploadDir = $this->scanDirectories($uploadDir);
        $output->writeln(count($filesInUploadDir) . " files found in 'uploads' folder");

        // Map for file in uploadDir
        $mapFilesUploadDir = [];

        // Loop into files in uploads
        foreach($filesInUploadDir as $file)
        {
            $name = basename($file);

            $mapFilesUploadDir[$name] = $file;

            // If file exist in server but not in database
            if (!array_key_exists($name, $mapFilesInDatabase)) {
                // This file sould be added in database
                $filesToAdd[] = $file;
            }
        }

        foreach ($filesInDatabase as $file) {
            // If file exist in database but not in server
            if (!array_key_exists($file->getName(), $mapFilesUploadDir)) {
                // This file sould be deleted in database
                $filesToDelete[] = $file;
            }
        }

        // Dry count
        if ($input->getOption('dry-count')) {
            $output->writeln("- " . count($filesToDelete) . " will be remove from database");
            $output->writeln("- " . count($filesToAdd) . " will be added to database");
            exit(0);
        }

        $output->writeln("");

        if (count($filesToAdd) === 0 && count($filesToDelete) === 0) {
            $output->writeln("Nothing to synchronize");
            exit(0);
        }

        $output->writeln("BEGIN synchronize");

        // Execute deletions
        foreach ($filesToDelete as $file) {
            $output->writeln("- Remove file id : " . $file->getId());
            $em->remove($file);
        }

        // Execute additions
        foreach ($filesToAdd as $file) {
            $name = basename($file);
            $webPath = "/uploads" . str_replace($uploadDir, "", $file);
            // For windows servers
            $webPath = str_replace("\\", "/", $webPath);
            $absolutePath = str_replace($name, "", $file);
            $fileToAdd = new File();
            $fileToAdd->setName($name)
                ->setAbsolutePath($absolutePath)
                ->setWebPath($webPath)
                ->setExtension(strtoupper(pathinfo($file)['extension']))
                ->setSize(filesize($file));

            $em->persist($fileToAdd);

            $output->writeln("- Add file name : " . $name);
        }

        if (!$input->getOption('dry-run')) {
            // Try doctrine flush
            try {
                $em->flush();
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
                exit(1);
            }
        } else {
            $output->writeln("**  The changes are not commit in dry-run mode **");
        }

        $output->writeln("END synchronize");
    }

    /**
     * Scan all files in a directory recursively
     *
     * @param $rootDir
     * @param array $allData
     * @return array
     */
    private function scanDirectories($rootDir, $allData = []) {
        // set filenames invisible if you want
        $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
        // run through content of root directory
        $dirContent = scandir($rootDir);
        foreach($dirContent as $key => $content) {
            // filter all files not accessible
            $path = $rootDir. DIRECTORY_SEPARATOR .$content;
            if(!in_array($content, $invisibleFileNames)) {
                // if content is file & readable, add to array
                if(is_file($path) && is_readable($path)) {
                    // save file name with path
                    $allData[] = $path;
                    // if content is a directory and readable, add path and name
                }elseif(is_dir($path) && is_readable($path)) {
                    // recursive callback to open new directory
                    $allData = $this->scanDirectories($path, $allData);
                }
            }
        }
        return $allData;
    }
}
