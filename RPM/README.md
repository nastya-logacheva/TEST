### **OTUS — Administrator Linux. Professional.**  
#### **Домашнее задание №8 — Сборка RPM-пакета и создание репозитория**

**Цель:**  
Научиться собирать собственные RPM-пакеты и создавать RPM-репозитории.

**Критерии приёма:**  
Статус «Принято» выставляется при выполнении следующих условий:
1. Собран RPM-пакет (например, для Apache или собственного приложения);
2. Создан репозиторий и размещён собранный RPM-файл;
3. Реализация выполнена либо в Vagrant, либо вручную с размещением через Nginx.

---

#### **Описание реализации**

Реализация выполнена по инструкции из методического материала:  
[Документация](https://docs.google.com/document/d/1yeYpcY39RxBGVIjBwTE12Y_VdjvsqCV3OFie2tvAtsg/edit?tab=t.0)

Все шаги автоматизированы с использованием Ansible и Vagrant.

Плейбук выполняет:
- установку зависимостей для сборки пакетов;
- подготовку структуры каталогов `~/rpmbuild` с помощью `rpmdev-setuptree`;
- скачивание и сборку исходников;
- генерацию собственного `.spec`-файла;
- сборку `.rpm` через `rpmbuild`;
- установку и настройку `createrepo`;
- публикацию локального репозитория на Nginx;
- настройку клиента на использование нового репозитория.

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
$ ansible-playbook -i hosts rpm.yml
```
Предварительно создаем файл *hosts*. \
---

#### **Вывод**

В результате:
- успешно собран RPM-пакет;
- развёрнут и протестирован собственный репозиторий;
- все шаги воспроизводимы с помощью Ansible.

Задание выполнено в соответствии с требованиями и готово к проверке.
