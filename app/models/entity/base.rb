module Entity
    class Base < ActiveRecord::Base
        include Utils::TmtLogger

        # This does not have a table associated.
        # Models will inherit this class and overrite the fields.
        self.abstract_class = true

        # def log message
        #     Repository::Base.log message
        # end

        # def warn message
        #     Repository::Base.warn message
        # end

    end
end
