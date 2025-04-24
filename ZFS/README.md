# ZFS

## Тестовый стенд:

ОС: Ubuntu 22.04 (generic/ubuntu2204)

CPU: 2

RAM: 2048 MB

### Задание
Определить алгоритм с наилучшим сжатием:

Определить какие алгоритмы сжатия поддерживает zfs (gzip, zle, lzjb, lz4);

создать 4 файловых системы на каждой применить свой алгоритм сжатия;

для сжатия использовать либо текстовый файл, либо группу файлов.

Определить настройки пула.

С помощью команды zfs import собрать pool ZFS.

Командами zfs определить настройки:
   
- размер хранилища;
    
- тип pool;
    
- значение recordsize;
   
- какое сжатие используется;
   
- какая контрольная сумма используется.

Работа со снапшотами:

скопировать файл из удаленной директории;

восстановить файл локально. zfs receive;

найти зашифрованное сообщение в файле secret_message.

## Выполнение

### 0. Проверка дисков
vagrant@zfs:~$ lsblk
NAME                      MAJ:MIN RM  SIZE RO TYPE MOUNTPOINTS
loop2                       7:2    0 53.3M  1 loop 
sda                         8:0    0  128G  0 disk 
├─sda1                      8:1    0    1M  0 part 
├─sda2                      8:2    0    2G  0 part /boot
└─sda3                      8:3    0  126G  0 part 
  └─ubuntu--vg-ubuntu--lv 253:0    0   63G  0 lvm  /
sdb                         8:16   0  512M  0 disk 
sdc                         8:32   0  512M  0 disk 
sdd                         8:48   0  512M  0 disk 
sde                         8:64   0  512M  0 disk 
sdf                         8:80   0  512M  0 disk 
sdg                         8:96   0  512M  0 disk 
sdh                         8:112  0  512M  0 disk 
sdi                         8:128  0  512M  0 disk 

### 1. Определение алгоритма с наилучшим сжатием

#### 1.1 Установка пакета утилит для zfs

 root@zfs:~# sudo apt install zfsutils-linux -y
 
#### 1.2. Создание пулов из двух дисков в режиме RAID 1

root@zfs:~# zpool create otus1 mirror /dev/sdb /dev/sdc

root@zfs:~# zpool create otus2 mirror /dev/sdd /dev/sde

root@zfs:~# zpool create otus3 mirror /dev/sdf /dev/sdg

root@zfs:~# zpool create otus4 mirror /dev/sdh /dev/sdi

#### 1.3 Проверяем пул:

root@zfs:~# zpool list

NAME    SIZE  ALLOC   FREE  CKPOINT  EXPANDSZ   FRAG    CAP  DEDUP    HEALTH  ALTROOT

otus1   480M   105K   480M        -         -     0%     0%  1.00x    ONLINE  -

otus2   480M   105K   480M        -         -     0%     0%  1.00x    ONLINE  -

otus3   480M   100K   480M        -         -     0%     0%  1.00x    ONLINE  -

otus4   480M   100K   480M        -         -     0%     0%  1.00x    ONLINE  -

#### 1.4 Проверяем статус:

root@zfs:~# zpool status

  pool: otus1
 
 state: ONLINE

config:

	NAME        STATE     READ WRITE CKSUM

  otus1       ONLINE       0     0     0

    mirror-0  ONLINE       0     0     0
	
      sdb     ONLINE       0     0     0
	  
      sdc     ONLINE       0     0     0

errors: No known data errors

  
  pool: otus2
 
 state: ONLINE

config:

	NAME        STATE     READ WRITE CKSUM

 otus2       ONLINE       0     0     0

   mirror-0  ONLINE       0     0     0
	
     sdd     ONLINE       0     0     0
	  
     sde     ONLINE       0     0     0


errors: No known data errors

  pool: otus3
 state: ONLINE
config:

	NAME        STATE     READ WRITE CKSUM
	otus3       ONLINE       0     0     0
	  mirror-0  ONLINE       0     0     0
	    sdf     ONLINE       0     0     0
	    sdg     ONLINE       0     0     0

errors: No known data errors

  pool: otus4
 
 state: ONLINE

config:

	NAME        STATE     READ WRITE CKSUM

 otus4       ONLINE       0     0     0

   mirror-0  ONLINE       0     0     0
	
     sdh     ONLINE       0     0     0
	  
     sdi     ONLINE       0     0     0

errors: No known data errors

#### 1.5 Добавляем разные алгоритмы сжатия:

root@zfs:~#  zfs set compression=lzjb otus1

root@zfs:~# zfs set compression=lz4 otus2

root@zfs:~# zfs set compression=gzip-9 otus3

root@zfs:~# zfs set compression=zle otus4

root@zfs:~# zfs get all | grep compression

#### 1.6 Проверяем методы сжатия:

otus1  compression           lzjb                   local

otus2  compression           lz4                    local

otus3  compression           gzip-9                 local

otus4  compression           zle                    local

#### 1.7 Скачивание тектового документа во все пулы: 
 
root@zfs:~# for i in {1..4}; do wget -P /otus$i https://gutenberg.org/cache/epub/2600/pg2600.converter.log; done

#### 1.8 Проверка файлов 

root@zfs:~# ls -l /otus*

/otus1:

total 22098

-rw-r--r-- 1 root root 41136901 Apr  2 07:31 pg2600.converter.log


/otus2:

total 18007

-rw-r--r-- 1 root root 41136901 Apr  2 07:31 pg2600.converter.log


/otus3:

total 10966

-rw-r--r-- 1 root root 41136901 Apr  2 07:31 pg2600.converter.log

/otus4:

total 40202

-rw-r--r-- 1 root root 41136901 Apr  2 07:31 pg2600.converter.log

#### 1.9 Проверяем размер каждого файла

root@zfs:~# zfs list

NAME    USED  AVAIL     REFER  MOUNTPOINT

otus1  21.7M   330M     21.6M  /otus1

otus2  17.7M   334M     17.6M  /otus2

otus3  10.9M   341M     10.7M  /otus3

otus4  39.4M   313M     39.3M  /otus4


root@zfs:~# zfs get all | grep compressratio | grep -v ref

otus1  compressratio         1.81x                  -

otus2  compressratio         2.23x                  -

otus3  compressratio         3.65x                  -

otus4  compressratio         1.00x                  -


Вывод: gzip-9 самый эффективный по сжатию

### 2. Определение настроек пула

#### 2.1 Скачиваем архив и разархивируем его

root@zfs:~# wget -O archive.tar.gz --no-check-certificate 'https://drive.usercontent.google.com/download?id=1MvrcEp-WgAQe57aDEzxSRalPAwbNN1Bb&export=download' 

root@zfs:~# tar -xzvf archive.tar.gz 

zpoolexport/

zpoolexport/filea

zpoolexport/fileb

Проверяем можно ли импортировать каталог

root@zfs:~# zpool import -d zpoolexport

   pool: otus
   
     id: 6554193320433390805
  
  state: ONLINE

status: Some supported features are not enabled on the pool.

 (Note that they may be intentionally disabled if the 'compatibility' property is set.)
 
 action: The pool can be imported using its name or numeric identifier, though
	
 some features will not be available without an explicit 'zpool upgrade'.
 
 config:

	otus                         ONLINE

   mirror-0                   ONLINE
	
     /root/zpoolexport/filea  ONLINE
	  
     /root/zpoolexport/fileb  ONLINE

#### 2.2 Импорт данного пула в ОС
 
 root@zfs:~# zpool import -d zpoolexport/ otus
 
root@zfs:~# zpool status
 
  pool: otus
 
 state: ONLINE

status: Some supported and requested features are not enabled on the pool.

 The pool can still be used, but some features are unavailable.
action: Enable all features using 'zpool upgrade'. Once this is done,
 the pool may no longer be accessible by software that does not support
 the features. See zpool-features(7) for details.

config:

	NAME                         STATE     READ WRITE CKSUM
 otus                         ONLINE       0     0     0

   mirror-0                   ONLINE       0     0     0
	
     /root/zpoolexport/filea  ONLINE       0     0     0
	  
     /root/zpoolexport/fileb  ONLINE       0     0     0

errors: No known data errors
 
#### 2.3 Определяем настройки 

root@zfs:~# zpool get all otus

NAME  PROPERTY                       VALUE                          SOURCE

otus  size                           480M                           -

otus  capacity                       0%                             -

otus  altroot                        -                              default

---------

otus  feature@device_rebuild         disabled                       local

otus  feature@zstd_compress          disabled                       local

otus  feature@draid                  disabled                       local

#### 2.4 Уточняем конкретный параметр

Размер:

root@zfs:~# zfs get available otus

NAME  PROPERTY   VALUE  SOURCE

otus  available  350M   -

Тип:

root@zfs:~# zfs get readonly otus

NAME  PROPERTY  VALUE   SOURCE

otus  readonly  off     default

Значение recordsize:

root@zfs:~# zfs get recordsize otus

NAME  PROPERTY    VALUE    SOURCE

otus  recordsize  128K     local

Тип контрольной суммы:

root@zfs:~# zfs get checksum otus

NAME  PROPERTY  VALUE      SOURCE

otus  checksum  sha256     local


### 3. Работа со снапшотом, поиск сообщения от преподавателя

#### 3.1 Скачиваем файл, указанный в задании:

root@zfs:~# wget -O otus_task2.file --no-check-certificate https://drive.usercontent.google.com/download?id=1wgxjih8YZ-cqLqaZVa0lA3h3Y029c3oI&export=download

[1] 7506

#### 3.2 Восстанавливаем файловую систему:

root@zfs:~# zfs receive otus/test@today < otus_task2.fil

#### 3.3 Ищем в каталоге secret_message

root@zfs:~# find /otus/test -name "secret_message"

/otus/test/task1/file_mess/secret_message

#### 3.4 Смотрим содержимое 

root@zfs:~# cat /otus/test/task1/file_mess/secret_message

https://otus.ru/lessons/linux-hl/
