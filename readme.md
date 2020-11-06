# How to start ?
(Relates to macOS High Sierra)
Download and install Vagrant, VirtualBox.
```SHELL
    mkdir gs_software_house && cd gs_software_house
    git clone https://github.com/laravel/homestead.git ~/Homestead
    cd Homestead 
    bash init.sh
```
Open in your favorit editor file .homestead/Homestead.yaml and edit like below:

```YAML
folders:
    - map: ~/gs_software_house
      to: /home/vagrant/projects

sites:
    - map: ocam.test
      to: /home/vagrant/projects/ocam/public
      php: "7.3" #required php version
```
And check (authorize, keys). If you don't have ssh keys [generate](https://selftaughtcoders.com/from-idea-to-launch/lesson-7/generate-ssh-key/) it

In directory Homestead: 
```SHELL
    vagrant up
    vagrant ssh
    sudo apt-get install php7.3-gmp
    composer install
    npm install
    
    php artisan key:generate
    php artisan migrate:fresh --seed
    php artisan config:clear (optional)
    
    remove storage folder from public folder and then: 
    php artisan storage:link
    
    set laravel-echo-server.json
    set env mail's configs
    run:
    npx laravel-echo-server start
```
Credentials for default user you can find in `database/seeds/TestDatabaseSeeder.php`
REMEMBER:
After you change Homestead.yaml configuration you should run vagrant reload --provision


# Relations
* user
    + activity_log
    + attachments
    + comments
        + attachment_comment
            + attachments 
                + comment_attachment
                    + comments
    + bill_timers
    + notifications
    + notification_subscriptions
    + notification_type_user
    + push_notifications
    + task_sort_orders
    + user_profile
        + images
    + user_tenant
        + personal_deadlines
        + filters
        + user_tenant_group
            + user_tenant_group_role
        + user_tenant_role
        + user_tenant_settings
            + images
        + user_tenant_task
        + user_tenant_templates
        + timers
            + pauses
            + timer_billings
                + bill_timers
            + timer_log
                + logs
                    + log_attachment
                        + attachments
                            + comment_attachment
                                + comments
        * tenants
            - members (users)
            + customers
                + bills
                    + bill_layouts
            + subscriptions
            + tenant_custom_role
            + tenant_priority
            + pipelines
                + pipeline_rules
                    + pipeline_filters
                + pipeline_rule_board
        + user_tenant_group
            + user_tenant_group_role
                + role
            + group
                + boards
                    + board_priority
                        + priorities
                    + budgets
                    + board_task
                        + tasks
                            * activity_log
                            * budgets
                            * comments
                            * notification_subscriptions
                            * sub_tasks
                            * task_sort_orders
                            * timers
