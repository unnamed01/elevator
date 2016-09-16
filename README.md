###Installation process###
1. Install [vagrant](https://www.vagrantup.com/docs/installation/).
2. Download [Vagrantfile](https://raw.githubusercontent.com/unnamed01/elevator/master/deploy/Vagrantfile) and [bootstrap.sh](https://raw.githubusercontent.com/unnamed01/elevator/master/deploy/bootstrap.sh) into a directory on your local pc.
3. Being inside that directory type "vagrant up"
4. Open your "/etc/hosts" file, add record "127.0.0.1 elevator.com". For Windows this file is located at C:\Windows\System32\drivers\etc\hosts
5. In browser open http://elevator.com:4568/emulator/