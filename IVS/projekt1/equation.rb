class Equation 
  def self.solve_quadratic(a, b, c)
    return nil if (a == 0 && b == 0) #Aeq0Beq0

    if (a == 0)   #Aeq0
      return [-c/b.to_f]
    end

    dis = (b*b) - (4*a*c)
    if dis == 0  #Eq0
      return [(-b/(2*a)).to_f]
    elsif (dis >0)   #Geq0
      x1 = (-b + Math.sqrt(dis))/(2*a)
      x2 = (-b - Math.sqrt(dis))/(2*a)
      return [x1,x2]
    elsif dis < 0 #Leq0
      return nil
      end
  end
end