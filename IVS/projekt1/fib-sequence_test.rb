require 'test/unit'
require_relative 'fib-sequence'

class FibonacciSequenceTest < Test::Unit::TestCase
  def setup
    @sequence = FibonacciSequence.new()
  end
  
  # do testu budou zahrnuty metody s prefixem "test_"
  def test_reset
    assert_equal(true,@sequence.reset())
  end

  def test_indexFNo
    assert_equal(55,@sequence.[](10))
  end

  def test_indexLeq0
    assert_equal(nil,@sequence.[](-1))
  end

  def test_reset_current
  @sequence.reset()
    assert_equal(nil, @sequence.current())
  end

  def test_reset_current_idx
    @sequence.reset()
    assert_equal(nil, @sequence.current_idx())
  end

  def test_next
    @sequence.[](0)
    assert_equal(1, @sequence.next())
  end

  def test_resetNext
    @sequence.[](5)
    @sequence.reset()
    assert_equal(0,@sequence.next())
  end

  def test_current
    @sequence.[](5)
    assert_equal(5,@sequence.current())
  end

  def test_current_idx
    @sequence.[](5)
    assert_equal(5,@sequence.current_idx())
  end

  def test_index_reset
    @sequence.[](6)
    @sequence.reset()
    assert_equal(5, @sequence.[](5))
  end
end