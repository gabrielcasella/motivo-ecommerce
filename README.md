# Motivo recruitment tech test

The request for this assignment was to develop a simple ecommerce backend where products could be added, updated and deleted.
In order to achieve that I decided to try Laravel as my application framework. Using Laravel means the code is structured on
the MVC architecture.

## Code structure

This particular assignment was to develop a RestfulAPI, so while an MVC framework was uses, no views were created.

Based on the MVC architecture the code is separated in models that connect to the database and controllers that run the
ecommerce logic.

For the database layer I decided to go with an EAV (entity, attribute, value) approach. I decided to go with this approach
as it provides flexibility to create as many unique attributes per product as needed without altering the Database structure.

In the database there is a list of products and a list of attributes. The attributes have metadata associated
with them so their type can be assigned dynamically. In order to store those attributes there is a separate table for each value
type (int, decimal, varchar, text, datetime). Those tables hold the relationship between products and attributes.

There are methods exposed by the api that allow the creation of products and attributes, updating them, as well as fetching and
deleting them.

These methods are implemented in the api controllers and are responsible to match a given product to its attributes.


## Libraries used

This project was build using a standard Laravel distribution, no other libraries were used.

That said, PHPUnit was used to run the unit test to ensure the desired outcome was achieved.


## Installation and testing steps (should be easy to test locally)

In order to install this solution Laravel has to be installed, and while that can be donde directly in any manchine
I recommend using a virtual environment. In order to do that easily for Laravel, homestead can be installed (for free):

- Install VirtualBox (https://www.virtualbox.org/)
- Install Vagrant (https://www.vagrantup.com/)
- Install git (https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
- Create a dedicated folder for Vagrant: 
```
mkdir motivo-vagrant; cd motivo-vagrant
```
- Add the Vagrant box for homestead: 
```
vagrant box add laravel/homestead
```
- Clone homestead:
 ```
 git clone https://github.com/laravel/homestead.git .
 ```
- Be sure to use the latest stable branch for homestead: ```git checkout release```
- Init homestead: 
```
bash init.sh
```
- Configure the host 
```
vim /etc/hosts
``` 
and add 
```
192.168.10.10 homestead.test
```
- Create keys: 
```
mkdir .ssh
ssh-keygen -t rsa -b 4096 -C "email@example.com" -f .ssh/id_rsa

```
- Create a folder for the project:
```
mkdir ~/ecommerce
```
- Configure Homestead.yaml
```
ip: "192.168.10.10"
memory: 2048
cpus: 2
provider: virtualbox

authorize: ~/motivo/.ssh/id_rsa.pub

keys:
    - ~/motivo/.ssh/id_rsa

folders:
    - map: ~/ecommerce/
      to: /home/vagrant/ecommerce
      type: "nfs"

sites:
    - map: homestead.test
      to: /home/vagrant/ecommerce/public

databases:
    - homestead

features:
    - mariadb: false
    - ohmyzsh: false
    - webdriver: false

hostname: homestead
```
- Bring Vagrant up
```
vagrant up
```
- Clone the project in the ecommerce folder created before:
```
get clone https://github.com/gabrielcasella/motivo-ecommerce.git
```

## Additions

An addition to the design was a product type, so a product can be simple (it is self contained) or configurable. A configurable
product consists of one or more simple products. For example, a shirt can be a configurable product that consists of 2 simple
products, one for size M and one for size L. With this approach we can have a general attributes for the configurable product
like a description, and the stock level and even the pricing can be maintained separate in each of the simple products associated
with it, that means we can easily visualise how many shirts size M are in the system and how many size L, for example.

## Future changes

## Additional comments