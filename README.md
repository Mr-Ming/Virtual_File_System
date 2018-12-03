# Virtual_File_System
Virtual File System written in PHP that doesn't actually create/delete/interact with your actually hard drive

## To Run The Program
1. Clone this branch `git clone https://github.com/Mr-Ming/Virtual_File_System.git`
2. cd into the root of the program directory `cd Virtual_File_System`
3. run `php virtual_terminal.php`

![screen shot 2018-12-02 at 10 57 11 pm](https://user-images.githubusercontent.com/2894340/49352416-c7532b80-f685-11e8-8776-0e7c62a1d090.png)

## Supported Commands
1. help : show all supported commands 
2. pwd : show current path 
3. mkdir(directory) : create new directory 
4. rmdir(directory) : remove existing directory 
5. cd(path) : change location to specified path 
6. symlink(source, dest) : add symlink 
7. rmsym(link) : remove symlink 
8. addfile(file) : add file to current directory 
9. listp(path) : list all files in given path 
10. listc : list all files in current directory 
11. dump : For debugging, dump the current file_system memory 
12. quit : Exit virtual terminal 

## Detail Example #1 (for mkdir / rmdir / cd / symlink / rmsym / dump / pwd )
1. Adding directory `usr` and `local` and then changing to that directory and dumping the memory for debugging
![screen shot 2018-12-02 at 11 02 44 pm](https://user-images.githubusercontent.com/2894340/49352559-7b54b680-f686-11e8-8055-e368a984e6b3.png)
2. Continue from #1: then perform a bunch of operation and ending with using a symlink
![screen shot 2018-12-02 at 11 06 01 pm](https://user-images.githubusercontent.com/2894340/49352628-da1a3000-f686-11e8-9da2-44a9f3ad5ee6.png)
3. Continue from #2: then remove the symlink, try cd using the removed symlink, then pwd
![screen shot 2018-12-02 at 11 13 51 pm](https://user-images.githubusercontent.com/2894340/49352810-07b3a900-f688-11e8-8b29-9a415c79c228.png)
