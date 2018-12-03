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

## Detail Example #2 (for mkdir/ addfile / listp / dump / cd )
1. Adding directory `usr/food` then add files `hotdog.jpg`, `hamburger.png`, `chicken.gif` to directory `food` and then add another directory `allergicFood` into directory `usr/food` then dump the memory for debugging

![screen shot 2018-12-02 at 11 35 34 pm](https://user-images.githubusercontent.com/2894340/49353292-033cbf80-f68b-11e8-9b02-6a494761bbd6.png)

2. Continue from #1, add directory `veggie/green` in `usr` then inside `green` add `salad.jpg` and also another directory called `sauce` and inside `sauce` add `mayonaise.jpg` then finally move to root by doing `cd(/)` and dump the memory

![screen shot 2018-12-02 at 11 37 06 pm](https://user-images.githubusercontent.com/2894340/49353337-32ebc780-f68b-11e8-8687-ef62551f6ebb.png)

3. Continue from #2, now from root, list the file in `food`, `usr`, `veggie`, `green`, `sauce`

** Notice 
> listp(/usr/food/veggie)
>> No file found in given path 

This is correct because `veggie` only contains directory and no files, but its child `veggie/green` contains the file

![screen shot 2018-12-02 at 11 40 54 pm](https://user-images.githubusercontent.com/2894340/49353448-b9a0a480-f68b-11e8-85fc-fd8924fb8b4f.png)

## Detail Example #3 (for mkdir / addfile / cd / listc / quit)
1. Adding directory `/usr/picture` then inside it add the file `banana.jpg` 

![screen shot 2018-12-02 at 11 45 30 pm](https://user-images.githubusercontent.com/2894340/49353553-5e22e680-f68c-11e8-802b-800bb5c5b980.png)

2. cd to root by `cd(/)` then use listc within directory root, `/usr/picture`, then `cd (..)`, and finally listc there

![screen shot 2018-12-02 at 11 48 35 pm](https://user-images.githubusercontent.com/2894340/49353643-d093c680-f68c-11e8-8533-17a89a38f3b7.png)

3. quit the terminal

![screen shot 2018-12-02 at 11 49 06 pm](https://user-images.githubusercontent.com/2894340/49353661-df7a7900-f68c-11e8-99cd-6f3a1e386884.png)





