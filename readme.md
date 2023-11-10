
## Bugfix for compatibility issue between numpy and tensorflow 2.5 

Manual adjustments to tensorflow file according to: https://github.com/tensorflow/models/issues/9706#issuecomment-792106149

File: PYTHON_ENV_PATH + /lib/python3.7/site-packages/tensorflow/python/ops

Import: from tensorflow.math import reduce_prod

in function: _constant_if_small

change: "if np.prod(shape) < 1000:" to "if reduce_prod(shape) < 1000:"