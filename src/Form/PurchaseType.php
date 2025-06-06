<?php
namespace App\Form;

use App\Entity\Purchase;
use App\Entity\TokenPack;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $opts): void
    {
        $b->add('tokenPack', EntityType::class, [
            'class'        => TokenPack::class,
            'choice_label' => 'name',
            'label'        => 'Pack de jetons',
        ]);
    }

    public function configureOptions(OptionsResolver $r): void
    {
        $r->setDefaults(['data_class' => Purchase::class]);
    }
}
