/* 
 * Copyright 2012 ClinRisk Ltd.
 * 
 * This file is part of QRISK2-2012 (http://qrisk.org, http://svn.clinrisk.co.uk/qrisk2).
 * 
 * QRISK2-2012 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * QRISK2-2012 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with QRISK2-2012.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * The initial version of this file, to be found at http://svn.clinrisk.co.uk/qrisk2, faithfully implements QRISK2-2012.
 * We have released this code under the GNU Lesser General Public License to enable others to implement the algorithm faithfully.
 * However, the nature of the GNU Lesser General Public License is such that we cannot prevent, for example, someone altering the coefficients.
 * We stress, therefore, that it is the responsibility of the end user to check that the source that they receive produces the same results as the original code posted at http://svn.clinrisk.co.uk/qrisk2.
 * Inaccurate implementations of risk scores can lead to wrong patients being given the wrong treatment.
 * 
 * This file has been auto-generated.
 * XML source: Q68_qrisk2_2012_1_0.xml
 * STATA dta time stamp: 2 Jan 2012 23:10
 * C file create date: Tue  3 Jan 2012 09:15:05 GMT
 */

#include <stdio.h>
#include <stdlib.h>
#include <clinrisk/Q68_qrisk2_2012_1_0.h>

static double score[16];
static char errorBuf[1024];
static int error;

void usage(void) {
	printf(" * Copyright 2012 ClinRisk Ltd.\n");
	printf(" * \n");
	printf(" * This is part of QRISK2-2012 (http://qrisk.org, http://svn.clinrisk.co.uk/qrisk2).\n");
	printf(" * \n");
	printf(" * QRISK2-2012 is free software: you can redistribute it and/or modify\n");
	printf(" * it under the terms of the GNU Lesser General Public License as published by\n");
	printf(" * the Free Software Foundation, either version 3 of the License, or\n");
	printf(" * (at your option) any later version.\n");
	printf(" * \n");
	printf(" * QRISK2-2012 is distributed in the hope that it will be useful,\n");
	printf(" * but WITHOUT ANY WARRANTY; without even the implied warranty of\n");
	printf(" * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n");
	printf(" * GNU Lesser General Public License for more details.\n");
	printf(" * \n");
	printf(" * You should have received a copy of the GNU Lesser General Public License\n");
	printf(" * along with QRISK2-2012.  If not, see <http://www.gnu.org/licenses/>.\n");
	printf(" * \n");
	printf(" * The initial version of this file, to be found at http://svn.clinrisk.co.uk/qrisk2, faithfully implements QRISK2-2012.\n");
	printf(" * We have released this code under the GNU Lesser General Public License to enable others to implement the algorithm faithfully.\n");
	printf(" * However, the nature of the GNU Lesser General Public License is such that we cannot prevent, for example, someone altering the coefficients.\n");
	printf(" * We stress, therefore, that it is the responsibility of the end user to check that the source that they receive produces the same results as the original code posted at http://svn.clinrisk.co.uk/qrisk2.\n");
	printf(" * Inaccurate implementations of risk scores can lead to wrong patients being given the wrong treatment.\n");
	printf(" *\n");
	printf(" * This file has been auto-generated.\n");
	printf(" * XML source: Q68_qrisk2_2012_1_0.xml\n");
	printf(" * STATA dta time stamp: 2 Jan 2012 23:10\n");
	printf(" * C file create date: Tue  3 Jan 2012 09:15:05 GMT\n");
	printf(" *\n");
	printf("Usage:\n");
	printf("  Q68_qrisk2_2012_1_0_commandLine age b_AF b_ra b_renal b_treatedhyp b_type2 bmi ethrisk fh_cvd rati sbp smoke_cat surv town\n");
}

int main (int argc, char *argv[]) {
	if (argc!=15) {
		usage();
		exit(1);
	}

	int age = atoi(argv[1]);
	int b_AF = atoi(argv[2]);
	int b_ra = atoi(argv[3]);
	int b_renal = atoi(argv[4]);
	int b_treatedhyp = atoi(argv[5]);
	int b_type2 = atoi(argv[6]);
	double bmi = atof(argv[7]);
	int ethrisk = atoi(argv[8]);
	int fh_cvd = atoi(argv[9]);
	double rati = atof(argv[10]);
	double sbp = atof(argv[11]);
	int smoke_cat = atoi(argv[12]);
	int surv = atoi(argv[13]);
	double town = atof(argv[14]);

	double score = cvd_female(age,b_AF,b_ra,b_renal,b_treatedhyp,b_type2,bmi,ethrisk,fh_cvd,rati,sbp,smoke_cat,surv,town,&error,errorBuf,sizeof(errorBuf));
	if (error) {
		printf("%s", errorBuf);
		exit(1);
	}
	printf("%f\n", score);
}
