class User < Entity::User
    extend Repository::Users

    has_many :achievements
    has_many :user_activities

    after_create :reward_account_creation


end
