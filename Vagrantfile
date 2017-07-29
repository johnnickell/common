# project settings
project_name = "common"
project_hostname = "common.dev"
project_root = "/vagrant"

# server settings
server_box = "bento/ubuntu-16.04"
server_ip = "192.168.50.21"
server_cpus = "1"
server_memory = "1024"
server_swap = "2048"

# vagrant config
Vagrant.configure(2) do |config|

    # server operating system
    config.vm.box = server_box

    # hostmanager plugin
    if Vagrant.has_plugin?("vagrant-hostmanager")
        config.hostmanager.enabled = true
        config.hostmanager.manage_host = true
        config.hostmanager.ignore_private_ip = false
        config.hostmanager.include_offline = false
    end

    # server networking
    if Vagrant.has_plugin?("vagrant-auto_network")
        config.vm.network :private_network, :ip => "0.0.0.0", :auto_network => true
    else
        config.vm.network :private_network, ip: server_ip
    end

    # server hostname and sync
    config.vm.hostname = project_hostname
    config.vm.synced_folder ".", project_root, id: "core"

    # virtualbox setup
    config.vm.provider :virtualbox do |vb|
        vb.name = project_name
        vb.customize ["modifyvm", :id, "--cpus", server_cpus]
        vb.customize ["modifyvm", :id, "--memory", server_memory]
        vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
        vb.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
    end

    # provisioning
    config.vm.provision :ansible do |ansible|
        ansible.playbook = "etc/provision/playbook.yml"
        ansible.limit = "all"
        ansible.groups = {
            "application"          => ["default"],
            "development:children" => ["application"]
        }
        ansible.extra_vars = {
            project_name: project_name,
            project_hostname: project_hostname,
            project_root: project_root,
            server_swap: server_swap
        }
    end

end
