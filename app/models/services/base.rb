module Services
    # This is the base class of all the external data services.
    class Base
    	extend Utils::TmtLogger

        require 'json'

        # Formats and indents json data
        # and allows one to debug API answers from the console.
        def self.format_pretty json
            JSON.pretty_generate(json)
        end

    end
end
