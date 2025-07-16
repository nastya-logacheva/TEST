### **OTUS — Administrator Linux. Professional.**  
#### **Домашнее задание №7 — Работа с NFS**

**Цель:**  
Научиться самостоятельно разворачивать сервис NFS и подключать к нему клиентов.

**Критерии приёма:**  
Статус «Принято» выставляется при выполнении следующих условий:
1. `vagrant up` поднимает две виртуальные машины: сервер и клиент;
2. на сервере настроена директория для раздачи по NFS;
3. на клиенте эта директория монтируется автоматически при запуске (через `/etc/fstab` или `autofs`);
4. в сетевой директории должна быть папка `upload` с правами на запись;
5. используется NFS версии 3.

---

#### **Описание реализации**

Реализация выполнена согласно методическим указаниям:  
[Документация](https://docs.google.com/document/d/1Xz7dCWSzaM8Q0VzBt78K3emh7zlNX3C-Q27B6UuVexI/edit?tab=t.0#heading=h.csr8pmcyj3iq)

Все действия автоматизированы с использованием Ansible.  
Плейбук:
- устанавливает необходимые пакеты на сервер и клиента,
- настраивает `exports` и монтирование через `fstab`,
- создаёт папку `upload` с правами на запись,
- выполняет перезапуск служб и монтирование ресурсов.

Также плейбук создает тестовые файлы, выполняет проверку монтирования и выводит содержимое каталога.


---
##### Подготовка стенда.
```
$ ansible --version
ansible [core 2.16.3]
  config file = None
  configured module search path = ['/home/yup/.ansible/plugins/modules', '/usr/share/ansible/plugins/modules']
  ansible python module location = /usr/lib/python3/dist-packages/ansible
  ansible collection location = /home/yup/.ansible/collections:/usr/share/ansible/collections
  executable location = /usr/bin/ansible
  python version = 3.12.3 (main, Feb  4 2025, 14:48:35) [GCC 13.3.0] (/usr/bin/python3)
  jinja version = 3.1.2
  libyaml = True

$ vagrant -v
Vagrant 2.4.3
```
Vagrantfile - generic/ubuntu2204\
Предварительно создаем файл *hosts*. \

---
##### Запуск стенда. Запуск локального скрипта и playbook.
Запускаем стенд:
```
$ vagrant up
$ vagrant status
Current machine states:

nfss                      running (virtualbox)
nfsc                      running (virtualbox)

This environment represents multiple VMs. The VMs are all listed
above with their current state. For more information about a specific
VM, run `vagrant status NAME`.
```
Запускаем ansible-playbook:
```
$ ansible-playbook -i hosts nfs.yml
```
Результат выполенения плейбука в файле ansible-result.txt
