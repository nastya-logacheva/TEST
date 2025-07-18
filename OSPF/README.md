### **OTUS — Administrator Linux. Professional.**  
#### **Домашнее задание №22 — OSPF**

**Цель:**  
Создать сетевую лабораторию и научиться настраивать динамическую маршрутизацию с помощью протокола OSPF на базе Quagga.

---

#### **Задание:**

1. Поднять три виртуальные машины;
2. Объединить их в разные VLAN;
3. Настроить OSPF между машинами (через Quagga);
4. Изобразить асимметричный маршрут;
5. Сделать один из линков "дорогим", при этом маршрутизация должна быть симметричной.

---

#### **Ход выполнения**

1. **Подготовка стенда:**
   - Использован Vagrant и ansible-провижининг;
   - Машины:
     - `r1`, `r2`, `r3` — все с Quagga и несколькими интерфейсами;
   - Стенд развёрнут по инструкции из методического указания:
     [Методичка по OSPF](https://docs.google.com/document/d/1c3p-2PQl-73G8uKJaqmyCaw_CtRQipAt/edit)

2. **Сетевые настройки:**
   - Между машинами созданы виртуальные каналы (vlan-соединения);
   - Установлены статические IP на интерфейсах;
   - Пример: 
     ```
     r1: eth1 192.168.10.1/30 ↔ eth1 r2: 192.168.10.2/30
     r1: eth2 192.168.20.1/30 ↔ eth2 r3: 192.168.20.2/30
     r2: eth3 192.168.30.1/30 ↔ eth3 r3: 192.168.30.2/30
     ```

3. **Настройка OSPF через Quagga:**
   - Конфигурация `zebra.conf` и `ospfd.conf` создана на каждой машине;
   - Пример настройки OSPF:
     ```
     router ospf
       network 192.168.10.0/30 area 0
       network 192.168.20.0/30 area 0
     ```
   - Установлена связность между всеми узлами через OSPF.

4. **Асимметричный маршрут:**
   - Для имитации асимметрии удалён один маршрут:
     ```
     ip route del ...
     ```
   - Проверка трассировки с `r1` на `r3` и обратно:
     ```
     traceroute 192.168.20.2
     ```

5. **Настройка стоимости линков (cost):**
   - На одном интерфейсе вручную увеличена стоимость:
     ```
     interface eth3
       ip ospf cost 100
     ```
   - В результате трафик начал идти по симметричному, но менее "дорогому" пути.

6. **Проверка:**
   - Таблицы маршрутов OSPF:
     ```
     show ip ospf route
     ```
   - Пинги и трассировки прошли успешно.

---

#### **Вывод**

В результате:
- реализована маршрутизация OSPF между тремя машинами;
- проверена асимметрия и управляемость маршрутов через `cost`;
- применена настройка VLAN и корректное объединение сетей;
- стенд протестирован и автоматизирован с помощью Ansible.
