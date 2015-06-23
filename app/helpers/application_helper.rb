module ApplicationHelper

    def self.pct value, total
        Integer(Float(value) / Float(total) * 100)
    end

end
