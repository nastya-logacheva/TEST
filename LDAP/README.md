### **OTUS — Administrator Linux. Professional.**  
#### **Домашнее задание №26 — LDAP **

**Цель:**  
Научиться устанавливать и настраивать LDAP-сервер (FreeIPA), а также конфигурировать LDAP-клиентов с помощью Ansible.

---

#### **Задание:**
1. Установить и настроить FreeIPA;
2. Написать Ansible-playbook для конфигурации клиента;
3. Проверить авторизацию пользователей через LDAP.

---

#### **Ход выполнения**

1. **Подготовка стенда:**
   - Использован Vagrant + Ansible;
   - Машины:
     - `ipa-server` — LDAP (FreeIPA);
     - `ipa-client` — клиент с Ansible-автоматизацией;
   - Убедились в наличии ansible:
     ```
     ansible --version
     ```

2. **Установка и настройка FreeIPA:**
   - Установлены пакеты:
     ```
     dnf install -y ipa-server ipa-server-dns
     ```
   - Запущена установка:
     ```
     ipa-server-install --unattended --realm=OTUS.LAB --domain=otus.lab      --ds-password=StrongPassw0rd --admin-password=StrongAdmin0rd      --hostname=ipa-server.otus.lab --setup-dns --no-forwarders
     ```

3. **Добавление пользователей в FreeIPA:**
   - Пример:
     ```
     ipa user-add testuser --first=Test --last=User --password
     ```

4. **Настройка клиента (через Ansible):**
   - Создан плейбук `ipa-client.yml`;
   - Задачи:
     - установка `ipa-client`;
     - автоматическая регистрация:
       ```
       ipa-client-install --unattended --domain=otus.lab --realm=OTUS.LAB          --server=ipa-server.otus.lab --principal=admin --password=StrongAdmin0rd
       ```
   - Плейбук успешно выполнен на `ipa-client`.

5. **Проверка подключения:**
   - Проверка авторизации:
     ```
     su - testuser
     ```
   - Проверка через `getent passwd` и `id testuser`;
   - Успешный вход с SSH:
     ```
     ssh testuser@ipa-client
     ```

---

#### **Вывод**

В результате:
- развернут сервер FreeIPA с DNS и LDAP;
- настроен клиент через Ansible;
- проверена авторизация пользователей;
- все компоненты работают согласно требованиям задания.
