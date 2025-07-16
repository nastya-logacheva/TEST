### **OTUS — Administrator Linux. Professional.**  
#### **Домашнее задание №9 — Работа с загрузчиком**

**Цель:**  
Научиться:
- попадать в систему без пароля (через Grub);
- устанавливать систему с LVM и переименовывать Volume Group.

**Критерии приёма:**  
1. Включено отображение меню загрузчика Grub;
2. Выполнен вход в систему без пароля несколькими способами;
3. Система установлена с LVM, VG переименован вручную.

---

#### **Описание реализации**

Реализация выполнена по инструкции из методического материала:  
[Документация](https://docs.google.com/document/d/1fRE_BFi-sFuLUUXKC8WHeGrFtPDXlTWznFgj3Z-xBM8/edit?tab=t.0)


---

#### **Ход выполнения**

Включить отображение меню Grub.
Редактируем файл /etc/default/grub:
```
root@ubuntu2204:~# nano /etc/default/grub
-
#GRUB_TIMEOUT_STYLE=hidden
GRUB_TIMEOUT=10
-
root@ubuntu2204:~# update-grub
-
Sourcing file `/etc/default/grub'
Sourcing file `/etc/default/grub.d/init-select.cfg'
Generating grub configuration file ...
Found linux image: /boot/vmlinuz-5.15.0-91-generic
Found initrd image: /boot/initrd.img-5.15.0-91-generic
Warning: os-prober will not be executed to detect other bootable partitions.
Systems on them will not be added to the GRUB boot configuration.
Check GRUB_DISABLE_OS_PROBER documentation entry.
done
-
```
Попасть в систему без пароля несколькими способами.
init=/bin/bash:  \
Перезагружаем систему и попадаем меню grub.\
Далее через клавишу "е" попадаем в окно редактиварония загрузки и добавляем в конец строки linux... init=/bin/bash:
```
linux  /vmlinuz-5.15.0-91-generic root=/dev/mapper/ubuntu-vg-ubuntu--lv ro = net.ifnames=o biosdevname=0 init=/bin/bash
```
Нажимает Ctrl+x или F10 для загрузки, перемонтируем систему для записи и меняем пароль на пользователе:
```
root@(none):/# mount -o remount,rw /
root@(none):/# passwd vagrant
New password:
Retype new password:
passwd: password updated successfully
```

Recovery mode: \
Перезагружаем систему и попадаем меню grub.\
Выбираем Advanced options - > (recovery mode) \
В этом меню сначала включаем поддержку сети (network) для того, чтобы файловая система перемонтировалась в режим read/write (либо это можно сделать вручную).
Далее выбираем пункт root и попадаем в консоль с пользователем root. Если вы ранее устанавливали пароль для пользователя root (по умолчанию его нет), то необходимо его ввести. 
В этой консоли можно производить любые манипуляции с системой. \
Повторяем смену пароля из предыдущего пункта.

Установить систему с LVM, после чего переименовать VG.
Смотрим текущее состояние системы:
```
root@ubuntu2204:~# vgs
  VG        #PV #LV #SN Attr   VSize    VFree 
  ubuntu-vg   1   1   0 wz--n- <126.00g 63.00g
```
Переименовываем:
```
root@ubuntu2204:~# vgrename ubuntu-vg ubuntu-otus

  Volume group "ubuntu-vg" successfully renamed to "ubuntu-otus"
```
Далее правим /boot/grub/grub.cfg. Везде заменяем старое название VG на новое (в файле дефис меняется на два дефиса ubuntu--vg ubuntu--otus).\
После чего можем перезагружаться и, если все сделано правильно, успешно грузимся с новым именем Volume Group и проверяем:
```
root@ubuntu2204:~# vgs
  VG          #PV #LV #SN Attr   VSize    VFree 
  ubuntu-otus   1   1   0 wz--n- <126.00g 63.00g
```

---

#### **Вывод**

В ходе выполнения задания:
- реализован вход в систему без пароля через Grub;
- выполнено переименование VG в LVM;
- действия документированы и воспроизводимы вручную.
