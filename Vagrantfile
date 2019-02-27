# Fix permissions after you run commands on both hosts and guest machine
if !Vagrant::Util::Platform.windows?
  system("
      if [ #{ARGV[0]} = 'up' ]; then
          echo 'Setting group write permissions for ./var/logs/*'
          chmod 775 ./var/logs
          chmod 664 ./var/logs/*
      fi
  ")
end

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.provider "virtualbox" do |v|
    v.memory = 2048
  end

  config.vm.box = "bento/ubuntu-18.04"
  config.vm.network "private_network", ip: "192.168.50.57"

  # Update apt packages
  config.vm.provision "shell", name: "apt", inline: <<-SHELL
    export DEBIAN_FRONTEND=noninteractive
    apt-get update && apt-get upgrade
    packagelist=(
      libssl1.0-dev
      libreadline-dev
      libyaml-dev
      libxml2-dev
      libxslt1-dev
      libnss3
      libx11-dev
      software-properties-common
      wget
      unzip
      curl
      ant
    )
    apt-get install -y ${packagelist[@]}
  SHELL

  # Update apt source for postgresql
  config.vm.provision "shell", name: "add postgres repo", inline: <<-SHELL
    PG_REPO_APT_SOURCE=/etc/apt/sources.list.d/pgdg.list
    if [ ! -f "$PG_REPO_APT_SOURCE" ]
    then
      # Add PG apt repo:
      echo "deb http://apt.postgresql.org/pub/repos/apt/ bionic-pgdg main" > "$PG_REPO_APT_SOURCE"

      # Add PGDG repo key:
      wget --quiet -O - https://apt.postgresql.org/pub/repos/apt/ACCC4CF8.asc | apt-key add -

      apt-get update
    fi

  SHELL

  #Install node/nvm/gulp
  config.vm.provision "shell", name: "install nvm and node", privileged: false, inline: <<-SHELL
    export DEBIAN_FRONTEND=noninteractive
    cd && curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.11/install.sh | bash
    export NVM_DIR="$HOME/.nvm"
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
    [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion
    nvm install node
    nvm use node
    npm install -g gulp
  SHELL

  # Install Yarn
  config.vm.provision "shell", name: "add yarn", inline: <<-SHELL

    YN_REPO_APT_SOURCE=/etc/apt/sources.list.d/yarn.list
    if [ ! -f "$YN_REPO_APT_SOURCE" ]
    then
      curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
      echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
    fi

    # https://github.com/yarnpkg/yarn/issues/2821
    apt-get purge -y cmdtest

    apt-get update && apt-get install -y  --no-install-recommends yarn

  SHELL

  config.vm.provision "shell", name: "install apache", inline: <<-'SHELL'
    apt-get install -y apache2
    #Allow port 80 through the firewall
    ufw allow 'Apache'
  SHELL

  # Make sure logs folder is owned by apache with group vagrant
  config.vm.synced_folder "var/logs", "/vagrant/var/logs", owner: "www-data", group: "vagrant"

  config.vm.provision "shell", name: "install php", inline: <<-SHELL

    # PHP and modules
    # Not all these packages may be required, it is just a list of the most common
    phppackagelist=(
      php-pear
      php-dev
      php-zip
      php-curl
      php-xml
      php-xmlrpc
      php-xmlwriter
      php-mbstring
      php-pgsql
      php-pdo
      php-gd
      php-intl
      php-xsl
      php-ast
      libapache2-mod-php
   )

   apt-get install -y php
   apt-get install -y ${phppackagelist[@]}
  SHELL

  # Install Composer (Globally)
  config.vm.provision "shell", name: "install composer", privileged: false, inline: <<-SHELL
     cd /vagrant && curl -sS https://getcomposer.org/installer | php
     sudo mv composer.phar /usr/local/bin/composer
  SHELL

  config.vm.provision "shell", name: "install postgres", inline: <<-SHELL

    # Postgres 11 and modules
    # Not all these packages may be required, it is just a list of the most common
    pgpackagelist=(
      postgresql
      libpq5
      postgresql-11
      postgresql-client-11
      postgresql-client-common
      postgresql-contrib
    )

    apt-get install -y   ${pgpackagelist[@]}

  SHELL

  config.vm.provision "shell", name: "configure postgres", inline: <<-SHELL
    sudo -u postgres psql -c "CREATE USER vagrant WITH SUPERUSER CREATEDB ENCRYPTED PASSWORD 'vagrant'"
    sudo -u postgres createdb vagrant
    # Make sure Postgres also runs after vagrant reload
    systemctl enable postgresql
  SHELL
  # Update Apache config and restart
  config.vm.provision "shell", name: "configure apache", inline: <<-'SHELL'

    # Symlink DocumentRoot o \Vagrant\Publics
    ln -s /vagrant/public /var/www/html/DocumentRoot

    sed -i -e "s/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/DocumentRoot/" /etc/apache2/sites-enabled/000-default.conf
    sed -i -e "s/AllowOverride None/AllowOverride All/" /etc/apache2/apache2.conf

    a2enmod rewrite
    apachectl restart
    # Make sure Apache also runs after vagrant reload
    systemctl enable apache2
  SHELL

  config.vm.post_up_message = <<MESSAGE

   You are now up and running.

   This is the >> Digest Authentication Server

   The URL is 192.168.50.57

MESSAGE

end
