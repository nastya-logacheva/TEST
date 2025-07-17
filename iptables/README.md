### **OTUS — Administrator Linux. Professional.**  
#### **Домашнее задание №21 — Сценарии iptables**

**Цель:**  
Закрепить навыки настройки iptables и продемонстрировать работу механизма knock-портов, проброса трафика и маршрутизации.

---

#### **Задание:**

1. Реализовать knock-последовательность:  
   - `centralRouter` может попасть по SSH на `inetRouter` через knock-скрипт.
2. Добавить `inetRouter2`, доступный с хоста:
   - через Host-Only или проброс портов.
3. Запустить `nginx` на `centralServer`.
4. Пробросить порт 80 с `centralServer` на порт 8080 на `inetRouter2`.
5. Сохранить дефолтный маршрут через `inetRouter`.

---

#### **Ход выполнения**

1. **Подготовка стенда:**
   - Использован модифицированный стенд из `oht19-network`;
   - Машины:
     - `inetRouter` — основной маршрутизатор;
     - `inetRouter2` — для проброса снаружи;
     - `centralRouter` — клиент;
     - `centralServer` — веб-сервер;
   - Ansible-роль и ручная проверка;

2. **Настройка knock-скрипта:**
   - На `inetRouter` iptables-блок SSH:
     ```
     iptables -A INPUT -p tcp --dport 22 -j DROP
     ```
   - Сценарий knock-портов:
     ```
     iptables -N KNOCKING
     iptables -A INPUT -p tcp --dport 1111 -m recent --name STEP1 --set -j DROP
     iptables -A INPUT -p tcp --dport 2222 -m recent --rcheck --name STEP1                -m recent --name STEP2 --set -j DROP
     iptables -A INPUT -p tcp --dport 3333 -m recent --rcheck --name STEP2                -j ACCEPT
     ```
   - На клиенте `centralRouter` — knock-скрипт:
     ```
     for port in 1111 2222 3333; do
         nmap -Pn --host-timeout 100ms --max-retries 0 -p $port 192.168.255.1
     done
     ssh 192.168.255.1
     ```

3. **Добавление inetRouter2:**
   - Установлен IP `192.168.255.14` и `192.168.56.13` (host-only);
   - Доступен с хоста напрямую.

4. **Запуск nginx на centralServer:**
   - Установлен nginx, открыт порт 80;
   - Проверка локально:
     ```
     curl http://localhost
     ```

5. **Проброс портов через iptables:**
   - На `inetRouter2`:
     ```
     iptables -t nat -A PREROUTING -p tcp --dport 8080        -j DNAT --to-destination 192.168.255.10:80
     iptables -A FORWARD -p tcp -d 192.168.255.10 --dport 80 -j ACCEPT
     ```
   - Проверка:
     ```
     curl http://192.168.56.13:8080
     ```

6. **Маршруты:**
   - На всех машинах установлен дефолт через `inetRouter`.

---

#### **Вывод**

В результате:
- реализован механизм knock для открытия SSH-доступа;
- добавлен внешний маршрутизатор с пробросом;
- nginx доступен снаружи через 8080 порт;
- все правила iptables протестированы;
- настройка выполнена через Ansible и вручную.
