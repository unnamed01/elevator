#!/usr/bin/env bash

aptitude update
aptitude safe-upgrade

aptitude install -y apache2
aptitude install -y php5 libapache2-mod-php5 php5-mcrypt

#aptitude install -y python-software-properties
sudo add-apt-repository ppa:ondrej/php -y
sudo aptitude update
sudo aptitude install -y php5.6

sudo aptitude install -y git

sudo aptitude install -y php-pear php5.6-dev php5.6-xml

sudo aptitude install -y gcc make autoconf pkg-config
sudo aptitude install -y libzmq-dev
sudo pecl install zmq-beta
sudo bash -c "echo extension=zmq.so > /etc/php/5.6/mods-available/zmq.ini"
sudo ln -s /etc/php/5.6/mods-available/zmq.ini /etc/php/5.6/cli/conf.d/30-zmq.ini
sudo ln -s /etc/php/5.6/mods-available/zmq.ini /etc/php/5.6/apache2/conf.d/30-zmq.ini

sudo aptitude install -y libtool pkg-config build-essential autoconf automake
sudo aptitude install -y libzmq-dev

mkdir /home/vagrant/downloads
cd /home/vagrant/downloads
wget https://github.com/zeromq/zeromq4-1/releases/download/v4.1.5/zeromq-4.1.5.tar.gz
tar xvzf zeromq-4.1.5.tar.gz
cd zeromq-4.1.5 && ./configure
sudo make install
sudo ldconfig
ldconfig -p | grep zmq





mkdir -p /home/vagrant/p/elevator && cd /home/vagrant/p/elevator
git clone https://github.com/unnamed01/elevator.git .

#apache2 virtual host
sudo mkdir -m0666 /var/log/apache2/elevator.com && sudo chown root:adm /var/log/apache2/elevator.com

sudo cp /home/vagrant/p/elevator/conf/010-elevator.conf /etc/apache2/sites-available/010-elevator.conf
sudo a2ensite 010-elevator

sudo a2dismod php5
sudo a2enmod php5.6

sudo service apache2 reload

sudo aptitude install -y zip unzip

cd /home/vagrant/downloads
wget "https://getcomposer.org/installer"
chmod a+x installer
php -f ./installer
sudo mv composer.phar /usr/local/bin/composer
rm -f ./installer
composer self-update

cd /home/vagrant/p/elevator && composer install

sudo mkdir -m0777 /home/vagrant/p/elevator/tmp
sudo chown -R vagrant:vagrant /home/vagrant/downloads /home/vagrant/p

php /home/vagrant/p/elevator/bin/push-server.php &


#git init
#git config --global user.email "unnamed01@gmail.com"
#git config --global user.name "unnamed"
#git add .
#git commit -m "First commit"
#git remote add origin https://github.com/unnamed01/elevator.git



#if ! [ -L /var/www ]; then
#  rm -rf /var/www
#  ln -fs /vagrant /var/www
#fi